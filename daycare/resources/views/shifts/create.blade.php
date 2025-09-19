@extends('layouts.app')

@push('styles')
<style>
    .shift-form-table {
        font-size: 0.9rem;
    }
    .shift-input {
        width: 80px;
        font-size: 0.8rem;
        text-align: center;
    }
    .day-header {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
        padding: 8px 4px;
        writing-mode: vertical-rl;
        text-orientation: mixed;
    }
    .weekend {
        background-color: #ffebee;
    }
    .sunday {
        background-color: #ffcdd2;
    }
    .user-row {
        background-color: #f8f9fa;
        font-weight: bold;
    }
    .auto-assign-preview {
        background-color: #e3f2fd;
        border: 2px dashed #2196f3;
    }
</style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>
        <i class="fas fa-calendar-plus me-2"></i>シフト作成
    </h3>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-warning" id="autoAssignBtn">
            <i class="fas fa-magic me-2"></i>自動割当
        </button>
        <a href="{{ route('shifts.index', ['month' => $yearMonth]) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>戻る
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            {{ substr($yearMonth, 0, 4) }}年{{ intval(substr($yearMonth, 4, 2)) }}月 シフト作成
        </h5>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ route('shifts.store') }}" id="shiftForm">
            @csrf
            <input type="hidden" name="year_month" value="{{ $yearMonth }}">

            <div class="mb-3">
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setShiftCode('早番')">早番</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setShiftCode('日勤')">日勤</button>
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="setShiftCode('遅番')">遅番</button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="setShiftCode('午前')">午前</button>
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="setShiftCode('午後')">午後</button>
                    <button type="button" class="btn btn-sm btn-outline-info" onclick="setShiftCode('短時間')">短時間</button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setShiftCode('休')">休</button>
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="clearSelectedCells()">クリア</button>
                </div>
                <small class="text-muted">セルを選択してからボタンをクリックしてください</small>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered shift-form-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">職員名</th>
                            @php
                                $targetDate = \Carbon\Carbon::createFromFormat('Ym', $yearMonth);
                                $daysInMonth = $targetDate->daysInMonth;
                            @endphp
                            @for($day = 1; $day <= $daysInMonth; $day++)
                                @php
                                    $currentDate = $targetDate->copy()->day($day);
                                    $dayOfWeek = $currentDate->dayOfWeek;
                                    $isWeekend = $dayOfWeek === 6;
                                    $isSunday = $dayOfWeek === 0;
                                    $dayClass = $isSunday ? 'sunday' : ($isWeekend ? 'weekend' : '');
                                @endphp
                                <th class="day-header {{ $dayClass }}">
                                    {{ $day }}<br><small>{{ $currentDate->format('D') }}</small>
                                </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td class="user-row">
                                    {{ $user->name }}<br>
                                    <small class="text-muted">{{ $user->position }}</small>
                                </td>
                                @for($day = 1; $day <= $daysInMonth; $day++)
                                    @php
                                        $currentDate = $targetDate->copy()->day($day);
                                        $dayOfWeek = $currentDate->dayOfWeek;
                                        $isWeekend = $dayOfWeek === 6;
                                        $isSunday = $dayOfWeek === 0;
                                        $dayClass = $isSunday ? 'sunday' : ($isWeekend ? 'weekend' : '');
                                    @endphp
                                    <td class="{{ $dayClass }}">
                                        <input type="text"
                                               class="form-control shift-input shift-cell"
                                               name="shifts[{{ $user->id }}_{{ $day }}][shift_code]"
                                               data-user-id="{{ $user->id }}"
                                               data-day="{{ $day }}"
                                               placeholder="-">
                                        <input type="hidden" name="shifts[{{ $user->id }}_{{ $day }}][user_id]" value="{{ $user->id }}">
                                        <input type="hidden" name="shifts[{{ $user->id }}_{{ $day }}][day]" value="{{ $day }}">
                                    </td>
                                @endfor
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>シフトを保存
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 自動割当結果モーダル -->
<div class="modal fade" id="autoAssignModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-magic me-2"></i>自動割当結果
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="autoAssignResult"></div>
                <div id="autoAssignWarnings" class="mt-3"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">キャンセル</button>
                <button type="button" class="btn btn-primary" id="applyAutoAssign">適用</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let selectedCells = [];
let autoAssignData = [];

// セル選択機能
$(document).on('click', '.shift-cell', function() {
    $(this).toggleClass('bg-info text-white');
    const cellId = $(this).data('user-id') + '_' + $(this).data('day');

    if ($(this).hasClass('bg-info')) {
        if (!selectedCells.includes(cellId)) {
            selectedCells.push(cellId);
        }
    } else {
        selectedCells = selectedCells.filter(id => id !== cellId);
    }
});

// シフトコード設定
function setShiftCode(code) {
    selectedCells.forEach(cellId => {
        $(`input[data-user-id="${cellId.split('_')[0]}"][data-day="${cellId.split('_')[1]}"]`).val(code);
    });
    clearSelection();
}

// 選択クリア
function clearSelectedCells() {
    selectedCells.forEach(cellId => {
        $(`input[data-user-id="${cellId.split('_')[0]}"][data-day="${cellId.split('_')[1]}"]`).val('');
    });
    clearSelection();
}

function clearSelection() {
    $('.shift-cell').removeClass('bg-info text-white');
    selectedCells = [];
}

// 自動割当
$('#autoAssignBtn').click(function() {
    $.ajax({
        url: '{{ route("shifts.auto-assign") }}',
        method: 'POST',
        data: {
            year_month: '{{ $yearMonth }}',
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            autoAssignData = response.assignments;
            showAutoAssignResult(response);
        },
        error: function(xhr) {
            alert('自動割当に失敗しました: ' + xhr.responseJSON.message);
        }
    });
});

function showAutoAssignResult(response) {
    let resultHtml = `
        <div class="alert alert-info">
            <strong>割当結果:</strong> ${response.summary.total_assignments}件のシフトを自動生成しました
        </div>
        <div class="row">
            <div class="col-md-6">
                <h6>割当件数: ${response.summary.total_assignments}件</h6>
            </div>
            <div class="col-md-6">
                <h6>警告: ${response.summary.total_warnings}件</h6>
            </div>
        </div>
    `;

    $('#autoAssignResult').html(resultHtml);

    if (response.warnings.length > 0) {
        let warningsHtml = '<div class="alert alert-warning"><strong>警告:</strong><ul class="mb-0">';
        response.warnings.forEach(warning => {
            warningsHtml += `<li>${warning}</li>`;
        });
        warningsHtml += '</ul></div>';
        $('#autoAssignWarnings').html(warningsHtml);
    } else {
        $('#autoAssignWarnings').html('');
    }

    $('#autoAssignModal').modal('show');
}

// 自動割当適用
$('#applyAutoAssign').click(function() {
    autoAssignData.forEach(assignment => {
        $(`input[data-user-id="${assignment.user_id}"][data-day="${assignment.day}"]`).val(assignment.shift_code);
    });
    $('#autoAssignModal').modal('hide');
});
</script>
@endpush