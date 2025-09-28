@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card attendance-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="fas fa-clock me-2"></i>勤怠打刻
                </h4>
                <div class="time-display current-time text-primary"></div>
            </div>

            <div class="card-body">
                <div class="text-center mb-4">
                    <h5>{{ $today->format('Y年n月j日 (D)') }}</h5>
                    <p class="text-muted">{{ auth()->user()->name }} さん</p>
                </div>

                @if($todayRecord)
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">出勤時刻</h6>
                                    <div class="time-display text-success">
                                        {{ $todayRecord->clock_in ? $todayRecord->clock_in : '未打刻' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">退勤時刻</h6>
                                    <div class="time-display text-danger">
                                        {{ $todayRecord->clock_out ? $todayRecord->clock_out : '未打刻' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">休憩開始</h6>
                                    <div class="time-display text-warning">
                                        {{ $todayRecord->break_start ? $todayRecord->break_start : '未打刻' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="card-title">休憩終了</h6>
                                    <div class="time-display text-warning">
                                        {{ $todayRecord->break_end ? $todayRecord->break_end : '未打刻' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('attendance.clock-in') }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-success btn-clock btn-clock-in"
                                    @if($todayRecord && $todayRecord->clock_in) disabled @endif>
                                <i class="fas fa-sign-in-alt me-2"></i>出勤
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('attendance.clock-out') }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-clock btn-clock-out"
                                    @if(!$todayRecord || !$todayRecord->clock_in || $todayRecord->clock_out) disabled @endif>
                                <i class="fas fa-sign-out-alt me-2"></i>退勤
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('attendance.break-start') }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-clock btn-break"
                                    @if(!$todayRecord || !$todayRecord->clock_in || $todayRecord->break_start) disabled @endif>
                                <i class="fas fa-coffee me-2"></i>休憩開始
                            </button>
                        </form>
                    </div>
                    <div class="col-md-6">
                        <form method="POST" action="{{ route('attendance.break-end') }}" class="d-grid">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-clock btn-break"
                                    @if(!$todayRecord || !$todayRecord->break_start || $todayRecord->break_end) disabled @endif>
                                <i class="fas fa-play me-2"></i>休憩終了
                            </button>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ route('attendance.monthly') }}" class="btn btn-outline-primary">
                        <i class="fas fa-calendar me-2"></i>勤怠履歴を確認
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection