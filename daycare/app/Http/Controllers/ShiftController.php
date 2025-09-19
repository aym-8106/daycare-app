<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ShiftController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'office.scope']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $yearMonth = $request->get('month', Carbon::now()->format('Ym'));

        // 対象月の日数を取得
        $targetDate = Carbon::createFromFormat('Ym', $yearMonth);
        $daysInMonth = $targetDate->daysInMonth;

        // 事業所の職員を取得
        $users = User::byOffice($user->office_id)
            ->active()
            ->orderBy('employee_id')
            ->get();

        // シフトデータを取得
        $shifts = Shift::byOffice($user->office_id)
            ->byMonth($yearMonth)
            ->with('user')
            ->get()
            ->keyBy(function ($shift) {
                return "{$shift->user_id}_{$shift->day}";
            });

        if ($request->get('format') === 'csv') {
            return $this->exportCsv($users, $shifts, $yearMonth, $daysInMonth);
        }

        return view('shifts.index', compact('users', 'shifts', 'yearMonth', 'daysInMonth'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403, 'シフト作成権限がありません');
        }

        $yearMonth = $request->get('month', Carbon::now()->format('Ym'));
        $users = User::byOffice($user->office_id)->active()->get();

        return view('shifts.create', compact('yearMonth', 'users'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403, 'シフト作成権限がありません');
        }

        $request->validate([
            'year_month' => 'required|string|size:6',
            'shifts' => 'required|array',
            'shifts.*.user_id' => 'required|exists:users,id',
            'shifts.*.day' => 'required|integer|min:1|max:31',
            'shifts.*.shift_code' => 'required|string|max:20',
            'shifts.*.start_time' => 'nullable|date_format:H:i',
            'shifts.*.end_time' => 'nullable|date_format:H:i',
        ]);

        foreach ($request->shifts as $shiftData) {
            Shift::updateOrCreate(
                [
                    'office_id' => $user->office_id,
                    'user_id' => $shiftData['user_id'],
                    'year_month' => $request->year_month,
                    'day' => $shiftData['day'],
                ],
                [
                    'shift_code' => $shiftData['shift_code'],
                    'start_time' => $shiftData['start_time'] ?? null,
                    'end_time' => $shiftData['end_time'] ?? null,
                    'note' => $shiftData['note'] ?? null,
                ]
            );

            AuditLog::log(
                $user->office_id,
                $user->id,
                'shifts',
                null,
                'create',
                $shiftData
            );
        }

        return redirect()->route('shifts.index', ['month' => $request->year_month])
            ->with('success', 'シフトを保存しました');
    }

    public function autoAssign(Request $request)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            abort(403, 'シフト自動割当権限がありません');
        }

        $request->validate([
            'year_month' => 'required|string|size:6',
        ]);

        $yearMonth = $request->year_month;
        $targetDate = Carbon::createFromFormat('Ym', $yearMonth);
        $daysInMonth = $targetDate->daysInMonth;

        $users = User::byOffice($user->office_id)
            ->active()
            ->with('office')
            ->get();

        $assignments = [];
        $warnings = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $currentDate = $targetDate->copy()->day($day);
            $dayOfWeek = $currentDate->dayOfWeek === 0 ? 7 : $currentDate->dayOfWeek; // 日曜=7

            // 営業日チェック
            if (!$user->office->isBusinessDay($dayOfWeek)) {
                continue;
            }

            // 利用可能な職員を取得
            $availableUsers = $users->filter(function ($u) use ($dayOfWeek) {
                return $u->canWorkOnDay($dayOfWeek);
            });

            if ($availableUsers->count() === 0) {
                $warnings[] = "{$day}日: 勤務可能な職員がいません";
                continue;
            }

            // 簡易割当ロジック（ローテーション）
            foreach ($availableUsers->take(2) as $index => $targetUser) {
                $shiftCode = $this->getShiftCode($index, $targetUser->employment_type);
                $times = $this->getShiftTimes($shiftCode);

                $assignments[] = [
                    'user_id' => $targetUser->id,
                    'day' => $day,
                    'shift_code' => $shiftCode,
                    'start_time' => $times['start'],
                    'end_time' => $times['end'],
                ];
            }

            // 人員不足チェック
            if ($availableUsers->count() < 2) {
                $warnings[] = "{$day}日: 人員不足（必要2名、利用可能{$availableUsers->count()}名）";
            }
        }

        return response()->json([
            'assignments' => $assignments,
            'warnings' => $warnings,
            'summary' => [
                'total_assignments' => count($assignments),
                'total_warnings' => count($warnings),
            ]
        ]);
    }

    private function getShiftCode($index, $employmentType)
    {
        if ($employmentType === 'part_time') {
            return ['短時間', '午前', '午後'][$index % 3] ?? '短時間';
        }

        return ['早番', '日勤', '遅番'][$index % 3] ?? '日勤';
    }

    private function getShiftTimes($shiftCode)
    {
        $times = [
            '早番' => ['start' => '08:30', 'end' => '17:30'],
            '日勤' => ['start' => '09:00', 'end' => '18:00'],
            '遅番' => ['start' => '10:00', 'end' => '19:00'],
            '午前' => ['start' => '09:00', 'end' => '13:00'],
            '午後' => ['start' => '13:00', 'end' => '17:00'],
            '短時間' => ['start' => '10:00', 'end' => '15:00'],
        ];

        return $times[$shiftCode] ?? ['start' => '09:00', 'end' => '18:00'];
    }

    private function exportCsv($users, $shifts, $yearMonth, $daysInMonth)
    {
        $filename = "シフト表_{$yearMonth}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($users, $shifts, $daysInMonth) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM

            // ヘッダー行
            $header = ['職員名'];
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $header[] = "{$day}日";
            }
            fputcsv($file, $header);

            // データ行
            foreach ($users as $user) {
                $row = [$user->name];
                for ($day = 1; $day <= $daysInMonth; $day++) {
                    $key = "{$user->id}_{$day}";
                    $shift = $shifts->get($key);
                    $row[] = $shift ? $shift->shift_code : '';
                }
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}