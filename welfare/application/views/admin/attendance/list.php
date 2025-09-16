<div class="content-wrapper">
    <section class="content-header">
        <h1>出退勤一覧ページ</h1>
    </section>
    <section class="content">
        <?php
        $prev_year = $year;
        $prev_month = $month - 1;
        if ($prev_month < 1) {
            $prev_month = 12;
            $prev_year--;
        }

        $next_year = $year;
        $next_month = $month + 1;
        if ($next_month > 12) {
            $next_month = 1;
            $next_year++;
        }
        ?>
        <div class="box-body">
            <div class="col-lg-12 col-xs-12">
                <form action="<?php echo base_url() ?>admin/attendance/index" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group text-center" style="display: flex; align-items: right; gap: 1rem; justify-content: right;">
                        <button type="button" id="excelExportButton" class="btn btn-primary">Excel出力</button>
                    </div>
                    <div class="form-group text-center" style="display: flex; align-items: center; gap: 1rem; justify-content: center;">
                        <a href="<?php echo base_url() ?>admin/attendance/index?year=<?php echo $prev_year ?>&month=<?php echo $prev_month ?>&company_id=<?php echo $company_id ?>" class="btn btn-default btn-sm month-btn prev-month">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        <select class="form-control" style="width: auto;" name="company_id" id="company_id" onchange="location.href='<?php echo base_url() ?>admin/attendance/index?year=<?php echo $year ?>&month=<?php echo $month ?>&company_id=' + this.value;">
                            <?php foreach ($company_list as $company): ?>
                                <option value="<?php echo $company['company_id'] ?>" <?php echo ($company['company_id'] == $company_id) ? 'selected' : '' ?>>
                                    <?php echo $company['company_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php echo ($year . '年 ' . $month . '月'); ?>
                        <a href="<?php echo base_url() ?>admin/attendance/index?year=<?php echo $next_year ?>&month=<?php echo $next_month ?>&company_id=<?php echo $company_id ?>" class="btn btn-default btn-sm month-btn next-month">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                    <div class="form-group">
                        <div class="box-body table-responsive no-padding" style="overflow: auto;">
                            <?php
                            $attendance_by_day = [];
                            $staff_list = [];

                            foreach ($month_attendance_data as $record) {
                                $day = (int)$record['work_date'];
                                $staff_id = $record['staff_id'];
                                $attendance_by_day[$day][$staff_id] = $record;

                                if (!isset($staff_list[$staff_id])) {
                                    $staff_list[$staff_id] = $record['staff_name'];
                                }
                            }
                            if (!empty($staff_list)) {
                            ?>
                                <table class="table table-hover table-bordered table-striped sticky-table">
                                    <thead>
                                        <tr>
                                            <th class="sticky-col">日付</th>
                                            <?php foreach ($staff_list as $staff_name): ?>
                                                <th><?php echo htmlspecialchars($staff_name, ENT_QUOTES, 'UTF-8'); ?></th>
                                            <?php endforeach; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($day = 1; $day <= $days; $day++): ?>
                                            <tr>
                                                <td class="sticky-col"><?php echo $day; ?>日</td>
                                                <?php foreach ($staff_list as $staff_id => $staff_name): ?>
                                                    <?php if (isset($attendance_by_day[$day][$staff_id])):
                                                    $data = $attendance_by_day[$day][$staff_id]; ?>
                                                        <td
                                                            data-toggle="modal"
                                                            data-target="#editAttendanceModal"
                                                            data-day="<?php echo $day; ?>"
                                                            data-attendance-id="<?php echo $data['attendance_id']; ?>"
                                                            data-staff-id="<?php echo $staff_id; ?>"
                                                            data-work-time="<?php echo $data['work_time']; ?>"
                                                            data-leave-time="<?php echo $data['leave_time']; ?>"
                                                            data-break-time="<?php echo $data['total_break_time']; ?>"
                                                            data-overtime-start="<?php echo $data['overtime_start_time']; ?>"
                                                            data-overtime-end="<?php echo $data['overtime_end_time']; ?>"
                                                            class="attendance-cell"
                                                        >
                                                            出勤: <?php echo $data['work_time']; ?><br>
                                                            退勤: <?php echo $data['leave_time']; ?><br>
                                                            休憩:
                                                            <?php echo ($data['total_break_time'] < 60) ? '1分未満' : floor($data['total_break_time'] / 60) . '分'; ?><br>
                                                            残業: <?php echo $data['overtime_start_time'] . '～' . $data['overtime_end_time']; ?>
                                                        </td>
                                                    <?php else: ?>
                                                        <td></td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>


<div class="modal fade" id="editAttendanceModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="attendanceEditForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editModalLabel">勤怠データを編集</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="閉じる">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="attendance_id" id="modalAttendanceId">
          <input type="hidden" name="staff_id" id="modalStaffId">
          <input type="hidden" name="work_date" id="modalWorkDate">

          <div class="mb-3">
            <label class="form-label">出勤時間</label>
            <input type="time" class="form-control" name="work_time" id="modalWorkTime">
          </div>
          <div class="mb-3">
            <label class="form-label">退勤時間</label>
            <input type="time" class="form-control" name="leave_time" id="modalLeaveTime">
          </div>
          <div class="mb-3">
            <label class="form-label">休憩時間（分）</label>
            <input type="number" class="form-control" name="break_time" id="modalBreakTime">
          </div>
          <div class="mb-3">
            <label class="form-label">残業開始</label>
            <input type="time" class="form-control" name="overtime_start_time" id="modalOvertimeStart">
          </div>
          <div class="mb-3">
            <label class="form-label">残業終了</label>
            <input type="time" class="form-control" name="overtime_end_time" id="modalOvertimeEnd">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">保存</button>
        </div>
      </div>
    </form>
  </div>
</div>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" />

<script type="text/javascript">
    $(function() {
        $('.datepicker').datetimepicker({
            locale: 'ja',
            format: 'HH:mm',
        });
    });
</script>

<script>
$(document).ready(function () {
    $('#editAttendanceModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // jQuery object
        $('#modalAttendanceId').val(button.data('attendance-id'));
        $('#modalStaffId').val(button.data('staff-id'));
        $('#modalWorkDate').val(button.data('day'));
        $('#modalWorkTime').val(button.data('work-time'));
        $('#modalLeaveTime').val(button.data('leave-time'));
        const breakSeconds = parseInt(button.data('break-time') || 0, 10);
        $('#modalBreakTime').val(breakSeconds < 60 ? '' : Math.floor(breakSeconds / 60));
        $('#modalOvertimeStart').val(button.data('overtime-start'));
        $('#modalOvertimeEnd').val(button.data('overtime-end'));
    });


    $('#attendanceEditForm').on('submit', function (e) {
        e.preventDefault();

        const workDate = $('#modalWorkDate').val(); // example: "2025-06-24" or "24"
        const year = <?= $year ?>;
        const month = <?= $month ?>;

        // If modalWorkDate only contains day (e.g. "24"), convert to YYYY-MM-DD
        const paddedDay = workDate.toString().padStart(2, '0');
        const fullDate = `${year}-${month.toString().padStart(2, '0')}-${paddedDay}`;

        function formatDateTime(date, time) {
            return `${date} ${time}:00`; // returns "YYYY-MM-DD HH:MM:SS"
        }

        const formData = {
            attendance_id: $('#modalAttendanceId').val(),
            staff_id: $('#modalStaffId').val(),
            work_date: fullDate,
            work_time: formatDateTime(fullDate, $('#modalWorkTime').val()),
            leave_time: formatDateTime(fullDate, $('#modalLeaveTime').val()),
            break_time: $('#modalBreakTime').val(), // still minutes
            overtime_start_time: formatDateTime(fullDate, $('#modalOvertimeStart').val()),
            overtime_end_time: formatDateTime(fullDate, $('#modalOvertimeEnd').val())
        };
        $.ajax({
            url: '<?= base_url() ?>admin/attendance/update_attendance',
            type: 'POST',
            dataType: 'json',
            data: formData,
            success: function (response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('保存に失敗しました');
                }
            },
            error: function () {
                alert('通信エラーが発生しました');
            }
        });
    });
    document.getElementById('excelExportButton').addEventListener('click', function () {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('admin/attendance/export_excel') ?>';

        const inputs = {
            year: '<?= $year ?>',
            month: '<?= $month ?>',
            company_id: '<?= $company_id ?>'
        };

        for (const name in inputs) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = inputs[name];
            form.appendChild(input);
        }

        document.body.appendChild(form);
        form.submit();
    }); 


});

</script>