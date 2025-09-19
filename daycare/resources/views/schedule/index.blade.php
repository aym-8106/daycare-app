@extends('layouts.app')

@push('styles')
<style>
    .schedule-table {
        font-size: 0.85rem;
    }
    .time-cell {
        width: 80px;
        font-weight: bold;
        background-color: #f8f9fa;
        text-align: center;
        vertical-align: middle;
    }
    .staff-cell {
        width: 150px;
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        padding: 8px;
    }
    .schedule-cell {
        min-height: 60px;
        padding: 4px;
        border: 1px solid #dee2e6;
        position: relative;
        cursor: pointer;
    }
    .schedule-item {
        padding: 4px 6px;
        border-radius: 4px;
        font-size: 0.75rem;
        line-height: 1.2;
        margin-bottom: 2px;
        min-height: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .activity-transport { background-color: #ffebee; border-left: 4px solid #f44336; }
    .activity-bath { background-color: #e3f2fd; border-left: 4px solid #2196f3; }
    .activity-rehab { background-color: #e8f5e8; border-left: 4px solid #4caf50; }
    .activity-meal { background-color: #fff3e0; border-left: 4px solid #ff9800; }
    .activity-recreation { background-color: #f3e5f5; border-left: 4px solid #9c27b0; }
    .activity-other { background-color: #f5f5f5; border-left: 4px solid #607d8b; }

    .alert-badge {
        position: absolute;
        top: 2px;
        right: 2px;
        font-size: 0.6rem;
        padding: 2px 4px;
    }
    .schedule-cell:hover {
        background-color: #f8f9fa;
    }
    .schedule-cell.editing {
        background-color: #fff3cd;
    }
    .client-name {
        font-weight: bold;
        color: #333;
    }
    .activity-name {
        color: #666;
        font-size: 0.7rem;
    }
    .memo-text {
        color: #888;
        font-size: 0.65rem;
        margin-top: 2px;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-calendar-day me-2"></i>日次スケジュール
    </h3>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" id="saveScheduleBtn">
            <i class="fas fa-save me-2"></i>保存
        </button>
        <button type="button" class="btn btn-outline-primary" id="clearAllBtn">
            <i class="fas fa-eraser me-2"></i>全クリア
        </button>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    {{ \Carbon\Carbon::parse($date)->format('Y年n月j日 (D)') }} スケジュール
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <form method="GET" action="{{ route('schedule.index') }}" class="d-inline-flex">
                    <input type="date" name="date" value="{{ $date }}" class="form-control me-2" style="width: 200px;">
                    <button type="submit" class="btn btn-primary">表示</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm schedule-table mb-0">
                <thead>
                    <tr>
                        <th class="time-cell">時間</th>
                        @foreach($staff as $staffMember)
                            <th class="staff-cell">
                                {{ $staffMember->name }}<br>
                                <small class="text-muted">{{ $staffMember->position }}</small>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($timeSlots as $timeSlot)
                        <tr>
                            <td class="time-cell">
                                {{ $timeSlot }}
                                @if(isset($alerts[$timeSlot]))
                                    @foreach($alerts[$timeSlot] as $alert)
                                        <span class="badge bg-{{ $alert['type'] }} alert-badge"
                                              title="{{ $alert['message'] }}">!</span>
                                    @endforeach
                                @endif
                            </td>
                            @foreach($staff as $staffMember)
                                @php
                                    $key = "{$timeSlot}_{$staffMember->id}";
                                    $schedule = $schedules->get($key);
                                @endphp
                                <td class="schedule-cell"
                                    data-time-slot="{{ $timeSlot }}"
                                    data-staff-id="{{ $staffMember->id }}"
                                    onclick="editSchedule(this)">
                                    @if($schedule)
                                        <div class="schedule-item activity-{{ $schedule->activity }}"
                                             style="{{ $schedule->color ? 'background-color: ' . $schedule->color . ';' : '' }}">
                                            @if($schedule->client_name)
                                                <div class="client-name">{{ $schedule->client_name }}</div>
                                            @endif
                                            <div class="activity-name">{{ $schedule->activity_name }}</div>
                                            @if($schedule->memo)
                                                <div class="memo-text">{{ $schedule->memo }}</div>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- アクティビティ凡例 -->
<div class="card mt-4">
    <div class="card-header">
        <h6 class="mb-0">
            <i class="fas fa-palette me-2"></i>アクティビティ凡例
        </h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="d-flex flex-wrap gap-2">
                    <div class="schedule-item activity-transport">送迎</div>
                    <div class="schedule-item activity-bath">入浴</div>
                    <div class="schedule-item activity-rehab">機能訓練</div>
                    <div class="schedule-item activity-meal">食事</div>
                    <div class="schedule-item activity-recreation">レクリエーション</div>
                    <div class="schedule-item activity-other">その他</div>
                </div>
            </div>
            <div class="col-md-6">
                <small class="text-muted">
                    ※ セルをクリックして編集できます<br>
                    ※ 右上の「!」マークは人員配置の警告を示します
                </small>
            </div>
        </div>
    </div>
</div>

<!-- スケジュール編集モーダル -->
<div class="modal fade" id="scheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">スケジュール編集</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="scheduleForm">
                    <div class="mb-3">
                        <label class="form-label">時間・職員</label>
                        <input type="text" class="form-control" id="timeStaffInfo" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="clientName" class="form-label">利用者名</label>
                        <input type="text" class="form-control" id="clientName" maxlength="100">
                    </div>
                    <div class="mb-3">
                        <label for="activity" class="form-label">アクティビティ</label>
                        <select class="form-select" id="activity" required>
                            <option value="transport">送迎</option>
                            <option value="bath">入浴</option>
                            <option value="rehab">機能訓練</option>
                            <option value="meal">食事</option>
                            <option value="recreation">レクリエーション</option>
                            <option value="other">その他</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="color" class="form-label">カスタムカラー（オプション）</label>
                        <input type="color" class="form-control form-control-color" id="color">
                    </div>
                    <div class="mb-3">
                        <label for="memo" class="form-label">メモ</label>
                        <textarea class="form-control" id="memo" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="deleteScheduleBtn">削除</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="saveScheduleItemBtn">保存</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let currentCell = null;
let scheduleData = {};

// スケジュール編集
function editSchedule(cell) {
    currentCell = cell;
    const timeSlot = $(cell).data('time-slot');
    const staffId = $(cell).data('staff-id');
    const staffName = $(cell).closest('tr').find('th').eq($(cell).index()).text().trim();

    $('#timeStaffInfo').val(`${timeSlot} - ${staffName}`);

    // 既存データを取得
    const scheduleItem = $(cell).find('.schedule-item');
    if (scheduleItem.length > 0) {
        $('#clientName').val(scheduleItem.find('.client-name').text() || '');
        $('#memo').val(scheduleItem.find('.memo-text').text() || '');

        // アクティビティを特定
        const activityClasses = scheduleItem.attr('class').split(' ');
        const activityClass = activityClasses.find(cls => cls.startsWith('activity-'));
        if (activityClass) {
            const activity = activityClass.replace('activity-', '');
            $('#activity').val(activity);
        }
    } else {
        $('#clientName').val('');
        $('#activity').val('transport');
        $('#color').val('#ffffff');
        $('#memo').val('');
    }

    $('#scheduleModal').modal('show');
}

// スケジュールアイテム保存
$('#saveScheduleItemBtn').click(function() {
    const timeSlot = $(currentCell).data('time-slot');
    const staffId = $(currentCell).data('staff-id');
    const clientName = $('#clientName').val();
    const activity = $('#activity').val();
    const color = $('#color').val();
    const memo = $('#memo').val();

    // データを保存
    if (!scheduleData[timeSlot]) {
        scheduleData[timeSlot] = {};
    }
    scheduleData[timeSlot][staffId] = {
        client_name: clientName,
        activity: activity,
        color: color,
        memo: memo
    };

    // セルを更新
    updateCell(currentCell, clientName, activity, color, memo);
    $('#scheduleModal').modal('hide');
});

// セル表示更新
function updateCell(cell, clientName, activity, color, memo) {
    const activityNames = {
        'transport': '送迎',
        'bath': '入浴',
        'rehab': '機能訓練',
        'meal': '食事',
        'recreation': 'レクリエーション',
        'other': 'その他'
    };

    let html = '';
    if (clientName || memo) {
        html = `<div class="schedule-item activity-${activity}" style="${color ? 'background-color: ' + color + ';' : ''}">`;
        if (clientName) {
            html += `<div class="client-name">${clientName}</div>`;
        }
        html += `<div class="activity-name">${activityNames[activity]}</div>`;
        if (memo) {
            html += `<div class="memo-text">${memo}</div>`;
        }
        html += '</div>';
    }

    $(cell).html(html);
}

// スケジュール削除
$('#deleteScheduleBtn').click(function() {
    const timeSlot = $(currentCell).data('time-slot');
    const staffId = $(currentCell).data('staff-id');

    if (scheduleData[timeSlot]) {
        delete scheduleData[timeSlot][staffId];
    }

    $(currentCell).html('');
    $('#scheduleModal').modal('hide');
});

// 全保存
$('#saveScheduleBtn').click(function() {
    const schedules = [];

    Object.keys(scheduleData).forEach(timeSlot => {
        Object.keys(scheduleData[timeSlot]).forEach(staffId => {
            const data = scheduleData[timeSlot][staffId];
            schedules.push({
                time_slot: timeSlot,
                staff_id: staffId,
                client_name: data.client_name,
                activity: data.activity,
                color: data.color,
                memo: data.memo
            });
        });
    });

    $.ajax({
        url: '{{ route("schedule.update") }}',
        method: 'POST',
        data: {
            schedules: schedules,
            work_date: '{{ $date }}',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            alert('スケジュールを保存しました');
        },
        error: function(xhr) {
            alert('保存に失敗しました: ' + xhr.responseJSON.message);
        }
    });
});

// 全クリア
$('#clearAllBtn').click(function() {
    if (confirm('全てのスケジュールをクリアしますか？')) {
        scheduleData = {};
        $('.schedule-cell').html('');
    }
});
</script>
@endpush