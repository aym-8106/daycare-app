@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <h2 class="mb-4">
            <i class="fas fa-tachometer-alt me-2"></i>ダッシュボード
            <small class="text-muted">{{ $today->format('Y年n月j日 (D)') }}</small>
        </h2>
    </div>
</div>

<div class="row">
    <!-- 本日の勤怠状況 -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2"></i>本日の勤怠状況
                </h5>
            </div>
            <div class="card-body">
                @if($todayAttendance)
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center">
                                <small class="text-muted d-block">出勤</small>
                                <strong class="h5 text-success">
                                    {{ $todayAttendance->clock_in ?: '未打刻' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <small class="text-muted d-block">退勤</small>
                                <strong class="h5 text-danger">
                                    {{ $todayAttendance->clock_out ?: '未打刻' }}
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <small class="text-muted d-block">休憩</small>
                                <strong class="h6">
                                    @if($todayAttendance->break_start && $todayAttendance->break_end)
                                        {{ $todayAttendance->break_start }} - {{ $todayAttendance->break_end }}
                                    @elseif($todayAttendance->break_start)
                                        休憩中
                                    @else
                                        未開始
                                    @endif
                                </strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <small class="text-muted d-block">労働時間</small>
                                <strong class="h6">
                                    @if($todayAttendance->work_minutes > 0)
                                        {{ floor($todayAttendance->work_minutes / 60) }}:{{ str_pad($todayAttendance->work_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                                    @else
                                        -
                                    @endif
                                </strong>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-clock fa-2x text-muted mb-3"></i>
                        <p class="text-muted">まだ出勤打刻がありません</p>
                    </div>
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('attendance.index') }}" class="btn btn-primary">
                        <i class="fas fa-clock me-2"></i>打刻画面へ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 重要なお知らせ -->
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-thumbtack me-2"></i>重要なお知らせ
                </h5>
                @if($unreadCount > 0)
                    <span class="badge bg-danger">{{ $unreadCount }}件未読</span>
                @endif
            </div>
            <div class="card-body">
                @if($pinnedMessages->count() > 0)
                    @foreach($pinnedMessages as $message)
                        <div class="border-bottom pb-2 mb-2">
                            <h6 class="mb-1">
                                <a href="{{ route('messages.show', $message) }}" class="text-decoration-none">
                                    {{ $message->title }}
                                </a>
                                @if($message->is_important)
                                    <span class="badge bg-danger ms-1">重要</span>
                                @endif
                            </h6>
                            <small class="text-muted">
                                {{ $message->created_at->format('m/d H:i') }} - {{ $message->user->name }}
                            </small>
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">重要なお知らせはありません</p>
                    </div>
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('messages.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-comments me-2"></i>掲示板を見る
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- 今月のシフト -->
    <div class="col-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-alt me-2"></i>今月のシフト予定
                </h5>
            </div>
            <div class="card-body">
                @if($monthlyShifts->count() > 0)
                    <div class="row">
                        @foreach($monthlyShifts->take(10) as $shift)
                            <div class="col-md-2 col-sm-3 col-4 mb-2">
                                <div class="text-center p-2 border rounded">
                                    <small class="text-muted d-block">{{ $shift->day }}日</small>
                                    <strong class="d-block">{{ $shift->shift_code }}</strong>
                                    @if($shift->start_time && $shift->end_time)
                                        <small class="text-muted">
                                            {{ $shift->start_time->format('H:i') }}-{{ $shift->end_time->format('H:i') }}
                                        </small>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($monthlyShifts->count() > 10)
                        <p class="text-center mt-2 mb-0">
                            <small class="text-muted">他 {{ $monthlyShifts->count() - 10 }}件</small>
                        </p>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">今月のシフトが登録されていません</p>
                    </div>
                @endif
                <div class="text-center mt-3">
                    <a href="{{ route('shifts.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar-alt me-2"></i>シフト管理へ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- クイックアクション -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>クイックアクション
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('attendance.index') }}" class="btn btn-success w-100 py-3">
                            <i class="fas fa-clock fa-2x d-block mb-2"></i>
                            勤怠打刻
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('shifts.index') }}" class="btn btn-primary w-100 py-3">
                            <i class="fas fa-calendar-alt fa-2x d-block mb-2"></i>
                            シフト確認
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('schedule.index') }}" class="btn btn-info w-100 py-3">
                            <i class="fas fa-calendar-day fa-2x d-block mb-2"></i>
                            本日の予定
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('messages.index') }}" class="btn btn-warning w-100 py-3">
                            <i class="fas fa-comments fa-2x d-block mb-2"></i>
                            掲示板
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection