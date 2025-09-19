<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'office.scope']);
    }

    public function index()
    {
        $today = Carbon::today();
        $user = auth()->user();

        $todayRecord = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        return view('attendance.index', compact('todayRecord', 'today'));
    }

    public function clockIn(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $record = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if ($record && $record->clock_in) {
            return back()->with('error', '既に出勤打刻済みです');
        }

        $record = AttendanceRecord::updateOrCreate(
            [
                'office_id' => $user->office_id,
                'user_id' => $user->id,
                'work_date' => $today,
            ],
            [
                'clock_in' => now()->format('H:i'),
                'status' => 'normal',
            ]
        );

        AuditLog::log(
            $user->office_id,
            $user->id,
            'attendance_records',
            $record->id,
            'create',
            ['action' => 'clock_in', 'time' => now()->format('H:i')]
        );

        return back()->with('success', '出勤打刻しました');
    }

    public function clockOut(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $record = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$record || !$record->clock_in) {
            return back()->with('error', '出勤打刻がありません');
        }

        if ($record->clock_out) {
            return back()->with('error', '既に退勤打刻済みです');
        }

        $record->update([
            'clock_out' => now()->format('H:i'),
            'break_minutes' => $record->break_minutes ?: $user->default_break_minutes,
            'work_minutes' => $record->calculateWorkMinutes(),
        ]);

        AuditLog::log(
            $user->office_id,
            $user->id,
            'attendance_records',
            $record->id,
            'update',
            ['action' => 'clock_out', 'time' => now()->format('H:i')]
        );

        return back()->with('success', '退勤打刻しました');
    }

    public function breakStart(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $record = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$record || !$record->clock_in) {
            return back()->with('error', '出勤打刻がありません');
        }

        if ($record->break_start) {
            return back()->with('error', '既に休憩開始済みです');
        }

        $record->update(['break_start' => now()->format('H:i')]);

        AuditLog::log(
            $user->office_id,
            $user->id,
            'attendance_records',
            $record->id,
            'update',
            ['action' => 'break_start', 'time' => now()->format('H:i')]
        );

        return back()->with('success', '休憩開始しました');
    }

    public function breakEnd(Request $request)
    {
        $user = auth()->user();
        $today = Carbon::today();

        $record = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        if (!$record || !$record->break_start) {
            return back()->with('error', '休憩開始がありません');
        }

        if ($record->break_end) {
            return back()->with('error', '既に休憩終了済みです');
        }

        $breakStart = Carbon::parse($record->break_start);
        $breakEnd = Carbon::now();
        $breakMinutes = $breakEnd->diffInMinutes($breakStart);

        $record->update([
            'break_end' => now()->format('H:i'),
            'break_minutes' => $breakMinutes,
        ]);

        AuditLog::log(
            $user->office_id,
            $user->id,
            'attendance_records',
            $record->id,
            'update',
            ['action' => 'break_end', 'time' => now()->format('H:i'), 'minutes' => $breakMinutes]
        );

        return back()->with('success', '休憩終了しました');
    }

    public function monthly(Request $request)
    {
        $user = auth()->user();
        $yearMonth = $request->get('month', Carbon::now()->format('Ym'));
        $targetUserId = $request->get('user_id', $user->id);

        if (!$user->isAdmin() && $targetUserId != $user->id) {
            abort(403, '他の職員の勤怠は閲覧できません');
        }

        $records = AttendanceRecord::byOffice($user->office_id)
            ->byMonth($yearMonth)
            ->where('user_id', $targetUserId)
            ->with('user')
            ->orderBy('work_date')
            ->get();

        $targetUser = $user->office->users()->find($targetUserId);

        if ($request->get('format') === 'csv') {
            return $this->exportCsv($records, $targetUser, $yearMonth);
        }

        return view('attendance.monthly', compact('records', 'targetUser', 'yearMonth'));
    }

    private function exportCsv($records, $user, $yearMonth)
    {
        $filename = "勤怠_{$user->name}_{$yearMonth}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM

            fputcsv($file, ['日付', '出勤', '退勤', '休憩開始', '休憩終了', '休憩時間', '実労働時間', '状況']);

            foreach ($records as $record) {
                fputcsv($file, [
                    $record->work_date->format('Y/m/d'),
                    $record->clock_in,
                    $record->clock_out,
                    $record->break_start,
                    $record->break_end,
                    $record->break_minutes . '分',
                    floor($record->work_minutes / 60) . '時間' . ($record->work_minutes % 60) . '分',
                    $this->getStatusName($record->status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function getStatusName($status)
    {
        $names = [
            'normal' => '正常',
            'late' => '遅刻',
            'early_leave' => '早退',
            'absent' => '欠勤',
        ];

        return $names[$status] ?? $status;
    }
}