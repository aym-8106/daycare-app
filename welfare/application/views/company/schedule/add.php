<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
          <i class="fa fa-users"></i> スケジュール追加
      </h1>
    </section>
    
    <section class="content">

        <div class="row">
            <div class="col-md-8">
                <?php
                $this->load->helper('form');
                $error = $this->session->flashdata('error');
                if($error)
                {
                    ?>
                    <div class="alert alert-danger alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('error'); ?>
                    </div>
                <?php } ?>
                <?php
                $success = $this->session->flashdata('success');
                if($success)
                {
                    ?>
                    <div class="alert alert-success alert-dismissable">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                <?php } ?>

                <div class="row">
                    <div class="col-md-12">
                        <?php echo validation_errors('<div class="alert alert-danger alert-dismissable">', ' <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button></div>'); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- left column -->
            <div class="col-md-8">
              <!-- general form elements -->

                <div class="box box-primary">
                    <?php $isEdit = !empty($schedule); ?>

                    <form role="form" id="addUser" action="<?php echo base_url() ?>company/schedule/schedule_save" method="post">
                        <input type="hidden" name="mode" value="save">
                        <div class="box-body">
                            <div class="row">
                                <!-- 事業所名 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_name">事業所名：</label>
                                        <select class="form-control required" id="post_company" name="companyId">
                                            <option value="">選択してください</option>
                                            <?php foreach ($company as $key => $value): ?>
                                                <option value="<?= $value['company_id'] ?>" <?= $isEdit && $schedule['company_id'] == $value['company_id'] ? 'selected' : '' ?>>
                                                    <?= $value['company_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- 主治医名 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="staff_name">主治医名：</label>
                                        <select class="form-control required" id="post_staff" name="staffId">
                                            <option value="">選択してください</option>
                                            <?php foreach ($staff as $key => $value): ?>
                                                <option value="<?= $value['staff_id'] ?>" <?= $isEdit && $schedule['staff_id'] == $value['staff_id'] ? 'selected' : '' ?>>
                                                    <?= $value['staff_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- 利用者名 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="patient_name">利用者名：</label>
                                        <select class="form-control required" id="post_patient" name="patientId">
                                            <option value="">選択してください</option>
                                            <?php foreach ($patient as $key => $value): ?>
                                                <option value="<?= $value['id'] ?>" <?= $isEdit && $schedule['patient_id'] == $value['patient_id'] ? 'selected' : '' ?>>
                                                    <?= $value['patient_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- 日付 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule_date">日付：</label>
                                        <input type="text" id="schedule_date" name="schedule_date" class="datepicker form-control required" value="<?= $isEdit ? $schedule['schedule_date'] : date('Y-m-d') ?>">
                                    </div>
                                </div>

                                <!-- 開始時間 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule_start_time">開始時間：</label>
                                        <input type="text" id="schedule_start_time" name="schedule_start_time" class="timepicker form-control required" value="<?= $isEdit ? $schedule['schedule_start_time'] : date('H:m') ?>">
                                    </div>
                                </div>
                                <!-- 終了時間 -->
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="schedule_end_time">終了時間：</label>
                                        <input type="text" id="schedule_end_time" name="schedule_end_time" class="timepicker form-control required" value="<?= $isEdit ? $schedule['schedule_end_time'] : date('H:m') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="box-footer">
                            <input type="submit" class="btn btn-primary" value="保存" />
                            <a class="btn btn-default" href="<?= base_url().'company/schedule/index/' ?>">戻る</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>    
    </section>
    
</div>

<script src="<?php echo base_url(); ?>assets/js/addUser.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
<!-- Select2 CSS & JS CDN 読み込み（head または footer に） -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $( function () {
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });
        $('.timepicker').datetimepicker({
            locale: 'ja',
            format: 'HH:mm',
            stepping: 15, // 15分単位で選べるように
            icons: {
                time: 'fa fa-clock',
                date: 'fa fa-calendar',
                up: 'fa fa-chevron-up',
                down: 'fa fa-chevron-down'
            },
            useCurrent: false,
            showClose: true,
            showClear: true,
            showTodayButton: false,
            toolbarPlacement: 'bottom',
            sideBySide: false,
            widgetPositioning: {
                horizontal: 'auto', // 'auto' / 'left' / 'right'
                vertical: 'bottom'   // 'auto' / 'top' / 'bottom'
            }
        });
    });
</script>

<script>
$(document).ready(function() {
    $('#post_company, #post_staff, #post_patient').select2({
        tags: true, // 新しい値の入力を許可
        placeholder: '選択または入力してください',
        allowClear: true,
        width: '100%', // Bootstrapとの整合性
        language: {
            noResults: function() {
                return "選択可能な情報がありません。";
            }
        }
    });
});
</script>

<script>
    $('#post_company').on('change', function () {
        const companyId = $(this).val();
        if (companyId) {
            $.ajax({
                url: '<?= base_url() ?>company/schedule/get_staff_data',
                method: 'POST',
                data: { company_id: companyId },
                dataType: 'json',
                success: function(response) {
                    $('#post_staff').empty();
                    $('#post_staff').append('<option value="">選択してください</option>');
                    $.each(response, function(index, staff) {
                        $('#post_staff').append('<option value="' + staff.staff_id + '">' + staff.staff_name + '</option>');
                    });
                },
                error: function () {
                    alert('情報の取得に失敗しました');
                }
            });
        }
    });
</script>

