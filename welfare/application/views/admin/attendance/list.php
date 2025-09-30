<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-calendar"></i> 出退勤管理
            <small>全スタッフの出退勤一覧</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">出退勤一覧 - <?php echo $year; ?>年<?php echo $month; ?>月</h3>
                        <div class="box-tools">
                            <form method="GET" class="form-inline">
                                <div class="form-group" style="margin-right: 10px;">
                                    <label>職員:</label>
                                    <select name="staff_id" class="form-control input-sm" onchange="this.form.submit()">
                                        <option value="all" <?php echo ($staff_id === 'all') ? 'selected' : ''; ?>>全職員</option>
                                        <?php foreach($all_staff as $staff): ?>
                                            <option value="<?php echo $staff['staff_id']; ?>"
                                                <?php echo ($staff_id == $staff['staff_id']) ? 'selected' : ''; ?>>
                                                <?php echo $staff['staff_name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin-right: 10px;">
                                    <label>年:</label>
                                    <select name="year" class="form-control input-sm" onchange="this.form.submit()">
                                        <?php for($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                                            <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>年
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>月:</label>
                                    <select name="month" class="form-control input-sm" onchange="this.form.submit()">
                                        <?php for($m = 1; $m <= 12; $m++): ?>
                                            <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                                                <?php echo $m; ?>月
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-success btn-sm pull-right" id="openExportModal">
                                    <i class="fa fa-download"></i> Excel出力
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="position: sticky; left: 0; background-color: #f4f4f4; z-index: 10; min-width: 80px;">日付</th>
                                        <?php
                                        // スタッフ一覧を取得（全職員 or 選択された職員）
                                        $staff_list = array();
                                        if ($staff_id === 'all') {
                                            // 全職員を表示
                                            foreach($all_staff as $staff) {
                                                $staff_list[$staff['staff_id']] = $staff['staff_name'];
                                            }
                                        } else {
                                            // 選択された職員のみ表示
                                            foreach($all_staff as $staff) {
                                                if ($staff['staff_id'] == $staff_id) {
                                                    $staff_list[$staff['staff_id']] = $staff['staff_name'];
                                                    break;
                                                }
                                            }
                                        }

                                        // ヘッダーにスタッフ名を表示
                                        foreach($staff_list as $s_id => $s_name): ?>
                                            <th style="min-width: 200px;"><?php echo $s_name; ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 日付ごとのデータを整理
                                    $attendance_by_day = array();
                                    foreach($month_attendance_data as $record) {
                                        $day = (int)$record['work_date'];
                                        $record_staff_id = $record['staff_id'];
                                        $attendance_by_day[$day][$record_staff_id] = $record;
                                    }

                                    // 各日の行を表示
                                    for($day = 1; $day <= $days; $day++): ?>
                                        <tr>
                                            <td style="position: sticky; left: 0; background-color: #f9f9f9; z-index: 10;">
                                                <strong><?php echo $day; ?>日</strong>
                                            </td>
                                            <?php foreach($staff_list as $s_id => $s_name): ?>
                                                <td style="min-width: 200px; font-size: 12px;">
                                                    <?php if(isset($attendance_by_day[$day][$s_id])):
                                                        $d = $attendance_by_day[$day][$s_id]; ?>
                                                        <div class="attendance-info">
                                                            <strong>出勤:</strong> <?php echo $d['work_time'] ? $d['work_time'] : '-'; ?><br>
                                                            <strong>退勤:</strong> <?php echo $d['leave_time'] ? $d['leave_time'] : '-'; ?><br>
                                                            <strong>休憩:</strong>
                                                            <?php
                                                            if (isset($d['total_break_time']) && $d['total_break_time'] > 0) {
                                                                if ($d['total_break_time'] < 60) {
                                                                    echo '1分未満';
                                                                } else {
                                                                    echo floor($d['total_break_time'] / 60) . '分';
                                                                }
                                                            } else {
                                                                echo '-';
                                                            }
                                                            ?><br>
                                                            <?php if(!empty($d['overtime_start_time']) && !empty($d['overtime_end_time']) && $d['overtime_start_time'] != '00:00' && $d['overtime_end_time'] != '00:00'): ?>
                                                                <strong>残業:</strong> <?php echo $d['overtime_start_time']; ?>～<?php echo $d['overtime_end_time']; ?>
                                                            <?php endif; ?>
                                                            <div class="edit-buttons" style="margin-top: 5px;">
                                                                <button class="btn btn-xs btn-warning edit-attendance-btn"
                                                                        data-attendance-id="<?php echo $d['attendance_id']; ?>"
                                                                        data-staff-name="<?php echo $s_name; ?>"
                                                                        data-work-date="<?php echo $year . '-' . sprintf('%02d', $month) . '-' . sprintf('%02d', $day); ?>">
                                                                    <i class="fa fa-edit"></i> 編集
                                                                </button>
                                                            </div>
                                                        </div>
                                                    <?php else: ?>
                                                        <span class="text-muted">-</span>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Excel出力モーダル -->
<div class="modal fade" id="exportModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="POST" action="<?php echo base_url('attendanceexport/export'); ?>" id="exportForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">Excel出力 - 職員選択</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <input type="hidden" name="year" value="<?php echo $year; ?>">
                    <input type="hidden" name="month" value="<?php echo sprintf('%02d', $month); ?>">
                    <input type="hidden" name="company_id" value="<?php echo $company_id; ?>">

                    <div class="form-group">
                        <label><strong>出力する職員を選択してください:</strong></label>
                        <div style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="selectAllStaff" checked> <strong>全員選択</strong>
                                </label>
                            </div>
                            <hr style="margin: 10px 0;">
                            <?php foreach($all_staff as $staff): ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="selected_staff[]" value="<?php echo $staff['staff_id']; ?>" class="staff-checkbox" checked>
                                        <?php echo $staff['staff_name']; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> CSV形式でダウンロードされます。Excelで開くことができます。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-download"></i> ダウンロード
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 出退勤編集モーダル -->
<div class="modal fade" id="editAttendanceModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="editAttendanceForm">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                    <h4 class="modal-title">出退勤編集</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_attendance_id" name="attendance_id">

                    <div class="form-group">
                        <label><strong>スタッフ名:</strong></label>
                        <span id="edit_staff_name" class="form-control-static"></span>
                    </div>

                    <div class="form-group">
                        <label><strong>勤務日:</strong></label>
                        <span id="edit_work_date" class="form-control-static"></span>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_work_time">出勤時間:</label>
                                <input type="time" class="form-control" id="edit_work_time" name="work_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_leave_time">退勤時間:</label>
                                <input type="time" class="form-control" id="edit_leave_time" name="leave_time">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_break_time">休憩時間（分）:</label>
                                <input type="number" class="form-control" id="edit_break_time" name="break_time" min="0" max="600">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_overtime_start_time">残業開始時間:</label>
                                <input type="time" class="form-control" id="edit_overtime_start_time" name="overtime_start_time">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_overtime_end_time">残業終了時間:</label>
                                <input type="time" class="form-control" id="edit_overtime_end_time" name="overtime_end_time">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="edit_reason">編集理由:</label>
                        <textarea class="form-control" id="edit_reason" name="edit_reason" rows="3" placeholder="編集理由を入力してください"></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fa fa-warning"></i> この編集操作はログとして記録されます。
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                    <button type="submit" class="btn btn-primary">保存</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.attendance-info {
    font-size: 11px;
    line-height: 1.3;
}
.table th, .table td {
    border: 1px solid #ddd;
    padding: 8px;
    vertical-align: top;
}
.table-responsive {
    border: 1px solid #ddd;
}
.edit-buttons {
    text-align: center;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Excel出力モーダルを開く
    $('#openExportModal').click(function() {
        $('#exportModal').modal('show');
    });

    // 全員選択チェックボックス
    $('#selectAllStaff').change(function() {
        $('.staff-checkbox').prop('checked', $(this).prop('checked'));
    });

    // 個別チェックボックスが変更されたら全員選択を解除
    $('.staff-checkbox').change(function() {
        if (!$(this).prop('checked')) {
            $('#selectAllStaff').prop('checked', false);
        } else {
            // 全てがチェックされているか確認
            var allChecked = $('.staff-checkbox:checked').length === $('.staff-checkbox').length;
            $('#selectAllStaff').prop('checked', allChecked);
        }
    });

    // 編集ボタンクリック時
    $('.edit-attendance-btn').click(function() {
        var attendanceId = $(this).data('attendance-id');
        var staffName = $(this).data('staff-name');
        var workDate = $(this).data('work-date');

        // モーダルに基本情報を設定
        $('#edit_attendance_id').val(attendanceId);
        $('#edit_staff_name').text(staffName);
        $('#edit_work_date').text(workDate);

        // 出退勤データを取得
        $.ajax({
            url: '<?php echo base_url('api/get_attendance_data'); ?>',
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: {
                attendance_id: attendanceId
            },
            success: function(response) {
            console.log('Response received:', response);
            if (response.success) {
                var data = response.data;

                // フィールドをクリア
                $('#edit_work_time').val('');
                $('#edit_leave_time').val('');
                $('#edit_break_time').val('');
                $('#edit_overtime_start_time').val('');
                $('#edit_overtime_end_time').val('');

                // 時刻フィールドに値を設定
                if (data.work_time && data.work_time !== '0000-00-00 00:00:00') {
                    var workTime = data.work_time.split(' ')[1]; // datetime から時刻部分を抽出
                    if (workTime && workTime !== '00:00:00') {
                        $('#edit_work_time').val(workTime.substring(0, 5));
                    }
                }
                if (data.leave_time && data.leave_time !== '0000-00-00 00:00:00') {
                    var leaveTime = data.leave_time.split(' ')[1]; // datetime から時刻部分を抽出
                    if (leaveTime && leaveTime !== '00:00:00') {
                        $('#edit_leave_time').val(leaveTime.substring(0, 5));
                    }
                }
                if (data.break_time !== null && data.break_time !== undefined) {
                    $('#edit_break_time').val(data.break_time);
                }
                if (data.overtime_start_time && data.overtime_start_time !== '0000-00-00 00:00:00') {
                    var overtimeStart = data.overtime_start_time.split(' ')[1]; // datetime から時刻部分を抽出
                    if (overtimeStart && overtimeStart !== '00:00:00') {
                        $('#edit_overtime_start_time').val(overtimeStart.substring(0, 5));
                    }
                }
                if (data.overtime_end_time && data.overtime_end_time !== '0000-00-00 00:00:00') {
                    var overtimeEnd = data.overtime_end_time.split(' ')[1]; // datetime から時刻部分を抽出
                    if (overtimeEnd && overtimeEnd !== '00:00:00') {
                        $('#edit_overtime_end_time').val(overtimeEnd.substring(0, 5));
                    }
                }

                // モーダルを表示
                $('#editAttendanceModal').modal('show');
            } else {
                alert('データの取得に失敗しました: ' + response.message);
            }
        },
        error: function(xhr, status, error) {
            console.log('AJAX Error:', xhr.responseText);
            alert('サーバーエラーが発生しました。詳細: ' + xhr.responseText);
        }
        });
    });

    // フォーム送信
    $('#editAttendanceForm').submit(function(e) {
        e.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
            url: '<?php echo base_url('api/update_attendance'); ?>',
            method: 'POST',
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            data: formData,
            success: function(response) {
                console.log('Update response:', response);
                if (response.success) {
                    alert('出退勤データが正常に更新されました。');
                    $('#editAttendanceModal').modal('hide');
                    // ページをリロードして最新のデータを表示
                    location.reload();
                } else {
                    alert('更新に失敗しました: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.log('Update AJAX Error:', xhr.responseText);
                alert('サーバーエラーが発生しました。詳細: ' + xhr.responseText);
            }
        });
    });
});
</script>