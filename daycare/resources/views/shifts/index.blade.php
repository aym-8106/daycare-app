@extends('layouts.app')

@push('styles')
<style>
    .shift-table {
        font-size: 0.9rem;
    }
    .shift-cell {
        min-width: 80px;
        text-align: center;
        padding: 4px;
    }
    .shift-code {
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 0.8rem;
        font-weight: bold;
    }
    .shift-早番 { background-color: #e3f2fd; color: #1976d2; }
    .shift-日勤 { background-color: #f3e5f5; color: #7b1fa2; }
    .shift-遅番 { background-color: #fff3e0; color: #f57c00; }
    .shift-午前 { background-color: #e8f5e8; color: #388e3c; }
    .shift-午後 { background-color: #fff8e1; color: #f9a825; }
    .shift-短時間 { background-color: #fce4ec; color: #c2185b; }
    .shift-休 { background-color: #f5f5f5; color: #757575; }
    .user-name {
        min-width: 120px;
        font-weight: bold;
    }
    .day-header {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        padding: 8px 4px;
    }
    .weekend {
        background-color: #ffebee;
    }
    .sunday {
        background-color: #ffcdd2;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-calendar-alt me-2"></i>シフト管理
    </h3>
    <div class="d-flex gap-2">
        @if(auth()->user()->isAdmin())
            <a href="{{ route('shifts.create', ['month' => $yearMonth]) }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>シフト作成
            </a>
        @endif
        <a href="{{ route('shifts.index', ['month' => $yearMonth, 'format' => 'csv']) }}" class="btn btn-outline-success">
            <i class="fas fa-download me-2"></i>CSV出力
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    {{ substr($yearMonth, 0, 4) }}年{{ intval(substr($yearMonth, 4, 2)) }}月 シフト表
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('shifts.index') }}" class="d-inline-flex">
                    <input type="month" name="month" value="{{ substr($yearMonth, 0, 4) }}-{{ substr($yearMonth, 4, 2) }}"
                           class="form-control me-2" style="width: 200px;">
                    <button type="submit" class="btn btn-primary">表示</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm shift-table mb-0">
                <thead>
                    <tr>
                        <th class="user-name">職員名</th>
                        @for($day = 1; $day <= $daysInMonth; $day++)
                            @php
                                $currentDate = \Carbon\Carbon::createFromFormat('Ym', $yearMonth)->day($day);
                                $dayOfWeek = $currentDate->dayOfWeek;
                                $isWeekend = $dayOfWeek === 6; // 土曜日
                                $isSunday = $dayOfWeek === 0; // 日曜日
                                $dayClass = $isSunday ? 'sunday' : ($isWeekend ? 'weekend' : '');
                            @endphp
                            <th class="day-header shift-cell {{ $dayClass }}">
                                {{ $day }}<br>
                                <small>{{ $currentDate->format('D') }}</small>
                            </th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td class="user-name p-3">
                                <div>{{ $user->name }}</div>
                                <small class="text-muted">{{ $user->position }}</small>
                            </td>
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $currentDate = \Carbon\Carbon::createFromFormat('Ym', $yearMonth)->day($day);
                                    $dayOfWeek = $currentDate->dayOfWeek;
                                    $isWeekend = $dayOfWeek === 6;
                                    $isSunday = $dayOfWeek === 0;
                                    $dayClass = $isSunday ? 'sunday' : ($isWeekend ? 'weekend' : '');

                                    $key = "{$user->id}_{$day}";
                                    $shift = $shifts->get($key);
                                @endphp
                                <td class="shift-cell {{ $dayClass }}">
                                    @if($shift)
                                        <div class="shift-code shift-{{ $shift->shift_code }}">
                                            {{ $shift->shift_code }}
                                        </div>
                                        @if($shift->start_time && $shift->end_time)
                                            <small class="d-block mt-1 text-muted">
                                                {{ $shift->start_time->format('H:i') }}-{{ $shift->end_time->format('H:i') }}
                                            </small>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            @endfor
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 凡例 -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-info-circle me-2"></i>シフト凡例
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex flex-wrap gap-2">
                    <span class="shift-code shift-早番">早番</span>
                    <span class="shift-code shift-日勤">日勤</span>
                    <span class="shift-code shift-遅番">遅番</span>
                    <span class="shift-code shift-午前">午前</span>
                    <span class="shift-code shift-午後">午後</span>
                    <span class="shift-code shift-短時間">短時間</span>
                    <span class="shift-code shift-休">休</span>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted">
                    ※ 早番：8:30-17:30、日勤：9:00-18:00、遅番：10:00-19:00<br>
                    ※ 午前：9:00-13:00、午後：13:00-17:00、短時間：10:00-15:00
                </small>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin())
<!-- 統計情報 -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <h5>登録職員数</h5>
                <h3>{{ $users->count() }}名</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body text-center">
                <h5>シフト登録済</h5>
                <h3>{{ $shifts->count() }}件</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body text-center">
                <h5>未登録日数</h5>
                @php
                    $totalCells = $users->count() * $daysInMonth;
                    $unassigned = $totalCells - $shifts->count();
                @endphp
                <h3>{{ $unassigned }}件</h3>
            </div>
        </div>
    </div>
</div>
@endif
@endsection