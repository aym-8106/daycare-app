<?php

namespace App\Http\Controllers;

use App\Models\DailySchedule;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'office.scope']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));

        // 時間スロットを30分刻みで生成
        $timeSlots = $this->generateTimeSlots('08:30', '17:30', 30);

        // 事業所の職員を取得
        $staff = User::byOffice($user->office_id)
            ->active()
            ->orderBy('employee_id')
            ->get();

        // 日次スケジュールを取得
        $schedules = DailySchedule::byOffice($user->office_id)
            ->byDate($date)
            ->with('staff')
            ->get()
            ->keyBy(function ($schedule) {
                return "{$schedule->time_slot}_{$schedule->staff_id}";
            });

        // 各時間帯の人員状況をチェック
        $alerts = $this->checkStaffingAlerts($timeSlots, $staff, $schedules);

        return view('schedule.index', compact('timeSlots', 'staff', 'schedules', 'date', 'alerts'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'schedules' => 'required|array',
            'schedules.*.time_slot' => 'required|string',
            'schedules.*.staff_id' => 'required|exists:users,id',
            'schedules.*.client_name' => 'nullable|string|max:100',
            'schedules.*.activity' => 'required|in:transport,bath,rehab,meal,recreation,other',
            'schedules.*.color' => 'nullable|string|max:7',
            'schedules.*.memo' => 'nullable|string',
            'work_date' => 'required|date',
        ]);

        foreach ($request->schedules as $scheduleData) {
            if (empty($scheduleData['client_name']) && empty($scheduleData['memo'])) {
                // 空のスケジュールは削除
                DailySchedule::byOffice($user->office_id)
                    ->where('work_date', $request->work_date)
                    ->where('time_slot', $scheduleData['time_slot'])
                    ->where('staff_id', $scheduleData['staff_id'])
                    ->delete();
                continue;
            }

            $schedule = DailySchedule::updateOrCreate(
                [
                    'office_id' => $user->office_id,
                    'work_date' => $request->work_date,
                    'time_slot' => $scheduleData['time_slot'],
                    'staff_id' => $scheduleData['staff_id'],
                ],
                [
                    'client_name' => $scheduleData['client_name'],
                    'activity' => $scheduleData['activity'],
                    'color' => $scheduleData['color'] ?: null,
                    'memo' => $scheduleData['memo'],
                ]
            );

            AuditLog::log(
                $user->office_id,
                $user->id,
                'daily_schedules',
                $schedule->id,
                'update',
                $scheduleData
            );
        }

        return response()->json(['success' => true, 'message' => 'スケジュールを更新しました']);
    }

    private function generateTimeSlots($startTime, $endTime, $intervalMinutes)
    {
        $slots = [];
        $current = Carbon::createFromFormat('H:i', $startTime);
        $end = Carbon::createFromFormat('H:i', $endTime);

        while ($current->lte($end)) {
            $slots[] = $current->format('H:i');
            $current->addMinutes($intervalMinutes);
        }

        return $slots;
    }

    private function checkStaffingAlerts($timeSlots, $staff, $schedules)
    {
        $alerts = [];

        foreach ($timeSlots as $timeSlot) {
            $assignedStaff = 0;
            $transportCount = 0;
            $bathCount = 0;

            foreach ($staff as $staffMember) {
                $key = "{$timeSlot}_{$staffMember->id}";
                $schedule = $schedules->get($key);

                if ($schedule && $schedule->client_name) {
                    $assignedStaff++;

                    if ($schedule->activity === 'transport') {
                        $transportCount++;
                    } elseif ($schedule->activity === 'bath') {
                        $bathCount++;
                    }
                }
            }

            // アラート条件をチェック
            if ($assignedStaff === 0) {
                $alerts[$timeSlot][] = [
                    'type' => 'warning',
                    'message' => '職員の配置がありません'
                ];
            } elseif ($assignedStaff < 2) {
                $alerts[$timeSlot][] = [
                    'type' => 'info',
                    'message' => '職員数が少ない可能性があります'
                ];
            }

            if ($transportCount > 0 && $assignedStaff < 2) {
                $alerts[$timeSlot][] = [
                    'type' => 'danger',
                    'message' => '送迎時は2名以上の配置が必要です'
                ];
            }

            if ($bathCount > 1) {
                $alerts[$timeSlot][] = [
                    'type' => 'warning',
                    'message' => '入浴介助の重複があります'
                ];
            }
        }

        return $alerts;
    }
}