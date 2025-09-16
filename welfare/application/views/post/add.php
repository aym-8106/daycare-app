<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>申し送りページ</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-4"></div>
            <div class="col-lg-4 col-xs-12">
                <form action="<?php echo base_url() ?>post/postsave" method="POST" id="form1" name="form1" class="form-horizontal">
                    <input type="hidden" name="mode" value="insert">
                    
                    <div class="form-group">
                        <label for="post_date" class="col-lg-3 col-sm-2 control-label">日付: </label>
                        <input type="text" id="post_date" name="post_date" class="datepicker1 form-control" value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="form-group">
                        <label for="patient_name" class="col-lg-3 col-sm-2 control-label">利用者名: </label>
                        <select class="form-control required" id="post_patient" name="patientId">
                            <option value="">選択してください</option>
                            <?php foreach ($patient as $key => $value): ?>
                                <option value="<?= $value['patient_id'] ?>"><?= $value['patient_name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- <div class="form-group">
                        <label for="patient_addr" class="col-lg-3 col-sm-2 control-label">住所: </label>
                        <input type="text" id="patient_addr" class="form-control" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label for="patient_date" class="col-lg-3 col-sm-2 control-label">曜日: </label>
                        <input type="hidden" id="patient_date" name="patient_date">
                        <input type="text" id="patient_date1" class="form-control" readonly>
                    </div>

                    <div class="form-group">
                        <label for="patient_curetype" class="col-lg-3 col-sm-2 control-label">サービス: </label>
                        <input type="hidden" id="patient_curetype" name="patient_curetype">
                        <input type="text" id="patient_curetype1" class="form-control" readonly>
                    </div> -->

                    <div class="form-group">
                        <label for="patient_usefrom" class="col-lg-3 col-sm-2 control-label">開始時間: </label>
                        <input type="text" id="patient_usefrom" name="patient_usefrom" class="datepicker form-control required" value="<?php echo $patient_usefrom; ?>">
                    </div>

                    <div class="form-group">
                        <label for="patient_useto" class="col-lg-3 col-sm-2 control-label">終了時間: </label>
                        <input type="text" id="patient_useto" name="patient_useto" class="datepicker form-control required" value="<?php echo $patient_useto; ?>">
                    </div>

                    <!-- <div class="form-group">
                        <label for="patient_repeat" class="col-lg-3 col-sm-2 control-label">頻度: </label>
                        <input type="hidden" id="patient_repeat" name="patient_repeat">
                        <input type="text" id="patient_repeat1" class="form-control" readonly>
                    </div> -->

                    <div class="form-group">
                        <label for="post_descript" class="col-lg-3 col-sm-2 control-label">申し送り内容: </label>
                        <textarea id="post_descript" name="post_descript" class="form-control"></textarea>
                    </div>
                    <div class="box-body">
                        <div class="col-lg-4 col-xs-2"></div>
                        <div class="col-lg-4 col-xs-8 text-center">
                            <input type="submit" class="btn btn-primary" value="保存" />
                            <a class="btn btn-default" href="<?php echo base_url().'post/index/'?>">戻る</a>
                        </div><!-- ./col -->
                        <div class="col-lg-4 col-xs-2"></div>
                    </div>
                </form>
            </div><!-- ./col -->
            <div class="col-lg-4"></div>
        </div>
    </section>
</div>

<!-- <script type="text/javascript" src="<?php //echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php //echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script> -->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.js.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>

<script type="text/javascript">
    $( function () {
      $('.datepicker1').datepicker({
        locale: 'ja',
        format: 'yyyy-mm-dd',
        autoclose: true,
      });
    });
</script>

<script>
    $( function () {
        $('.datepicker').datetimepicker({
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
    function fetchPatientData() {
        const patientId = $('#post_patient').val();
        const postDate = $('#post_date').val();

        if (patientId) {
            $.ajax({
                url: '<?= base_url() ?>post/get_patient_data',
                method: 'POST',
                data: { 
                    id: patientId,
                    post_date : postDate
                },
                dataType: 'json',
                success: function (data) {
                    $('#patient_usefrom').val(data.patient_usefrom);
                    $('#patient_useto').val(data.patient_useto);
                },
                error: function () {
                    
                }
            });
        }
    }
    $('#post_patient').on('change', fetchPatientData);

    function getPatientData() {
        const postDate = $('#post_date').val();

        $.ajax({
            url: '<?= base_url() ?>post/patient_today_data',
            method: 'POST',
            data: {
                post_date : postDate
            },
            dataType: 'json',
            success: function (data) {
                $('#post_patient').empty();
                $('#post_patient').append('<option value="">選択してください</option>');
                $.each(data, function (key, value) {
                    $('#post_patient').append('<option value="' + value.patient_id + '">' + value.patient_name + '</option>');
                });
                
                $('#patient_usefrom').val('00:00');
                $('#patient_useto').val('00:00');
            },
            error: function () {
                
            }
        });
    }
    $('#post_date').on('change', getPatientData);
</script>
