<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Message;
use App\Models\Shift;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'office.scope']);
    }

    public function index()
    {
        $user = auth()->user();
        $today = Carbon::today();

        // 本日の打刻状況
        $todayAttendance = AttendanceRecord::byOffice($user->office_id)
            ->where('user_id', $user->id)
            ->where('work_date', $today)
            ->first();

        // 重要なお知らせ（ピン留めメッセージ）
        $pinnedMessages = Message::byOffice($user->office_id)
            ->pinned()
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // 今月のシフト確認
        $currentMonth = Carbon::now()->format('Ym');
        $monthlyShifts = Shift::byOffice($user->office_id)
            ->byMonth($currentMonth)
            ->where('user_id', $user->id)
            ->orderBy('day')
            ->get();

        // 未読メッセージ数
        $unreadCount = Message::byOffice($user->office_id)
            ->whereDoesntHave('messageReads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();

        return view('dashboard', compact(
            'todayAttendance',
            'pinnedMessages',
            'monthlyShifts',
            'unreadCount',
            'today'
        ));
    }
}