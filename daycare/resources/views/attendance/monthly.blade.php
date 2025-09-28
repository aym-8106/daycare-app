@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-calendar-alt me-2"></i>月次勤怠確認
    </h3>
    <div>
        <a href="{{ route('attendance.monthly', ['month' => $yearMonth, 'user_id' => $targetUser->id, 'format' => 'csv']) }}"
           class="btn btn-success">
            <i class="fas fa-download me-2"></i>CSV出力
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">{{ $targetUser->name }} さんの勤怠記録</h5>
                <small class="text-muted">{{ substr($yearMonth, 0, 4) }}年{{ intval(substr($yearMonth, 4, 2)) }}月</small>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('attendance.monthly') }}" class="d-inline-flex">
                    <input type="hidden" name="user_id" value="{{ $targetUser->id }}">
                    <input type="month" name="month" value="{{ substr($yearMonth, 0, 4) }}-{{ substr($yearMonth, 4, 2) }}"
                           class="form-control me-2" style="width: 200px;">
                    <button type="submit" class="btn btn-primary">表示</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body">
        @if($records->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>日付</th>
                            <th>出勤</th>
                            <th>退勤</th>
                            <th>休憩開始</th>
                            <th>休憩終了</th>
                            <th>休憩時間</th>
                            <th>実労働時間</th>
                            <th>状況</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalWorkMinutes = 0;
                            $totalBreakMinutes = 0;
                        @endphp
                        @foreach($records as $record)
                            @php
                                $totalWorkMinutes += $record->work_minutes;
                                $totalBreakMinutes += $record->break_minutes;
                            @endphp
                            <tr>
                                <td class="fw-bold">
                                    {{ $record->work_date->format('m/d') }}
                                    <small class="text-muted">({{ $record->work_date->format('D') }})</small>
                                </td>
                                <td>
                                    @if($record->clock_in)
                                        <span class="badge bg-success">{{ $record->clock_in }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->clock_out)
                                        <span class="badge bg-danger">{{ $record->clock_out }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->break_start)
                                        <span class="badge bg-warning text-dark">{{ $record->break_start }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->break_end)
                                        <span class="badge bg-warning text-dark">{{ $record->break_end }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($record->break_minutes > 0)
                                        {{ $record->break_minutes }}分
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="fw-bold">
                                    @if($record->work_minutes > 0)
                                        {{ floor($record->work_minutes / 60) }}:{{ str_pad($record->work_minutes % 60, 2, '0', STR_PAD_LEFT) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusClass = match($record->status) {
                                            'normal' => 'bg-success',
                                            'late' => 'bg-warning text-dark',
                                            'early_leave' => 'bg-info',
                                            'absent' => 'bg-secondary',
                                            default => 'bg-light text-dark'
                                        };
                                        $statusName = match($record->status) {
                                            'normal' => '正常',
                                            'late' => '遅刻',
                                            'early_leave' => '早退',
                                            'absent' => '欠勤',
                                            default => $record->status
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusName }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-secondary">
                        <tr>
                            <th>合計</th>
                            <th colspan="4"></th>
                            <th>{{ $totalBreakMinutes }}分</th>
                            <th class="fw-bold">{{ floor($totalWorkMinutes / 60) }}:{{ str_pad($totalWorkMinutes % 60, 2, '0', STR_PAD_LEFT) }}</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h5>出勤日数</h5>
                            <h3>{{ $records->where('clock_in', '!=', null)->count() }}日</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h5>総労働時間</h5>
                            <h3>{{ floor($totalWorkMinutes / 60) }}時間{{ $totalWorkMinutes % 60 }}分</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <h5>平均労働時間</h5>
                            <h3>
                                @php
                                    $workDays = $records->where('clock_in', '!=', null)->count();
                                    $avgMinutes = $workDays > 0 ? floor($totalWorkMinutes / $workDays) : 0;
                                @endphp
                                {{ floor($avgMinutes / 60) }}時間{{ $avgMinutes % 60 }}分
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">勤怠記録がありません</h5>
                <p class="text-muted">この月の勤怠データが見つかりませんでした。</p>
            </div>
        @endif
    </div>
</div>
@endsection