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
                    <input type="hidden" name="mode" value="update">
                    <input type="hidden" name="post_id" value="<?php echo $post_data['id']; ?>">
                    <input type="hidden" name="post_content_id" value="<?php echo $post_data['post_id']; ?>">

                    <div class="form-group">
                        <label for="post_date" class="col-lg-3 col-sm-2 control-label">日付: </label>
                        <input type="text" id="post_date" name="post_date" class="datepicker1 form-control" readonly value="<?php echo $post_data['schedule_date']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="patient_addr" class="col-lg-3 col-sm-2 control-label">利用者名: </label>
                        <input type="text" class="form-control" id="patient_name" placeholder=""
                                name="patient_name" readonly value="<?php echo $post_data['patient_name']; ?>">
                        <input type="hidden" value="<?php echo $post_data['patient_id']; ?>"
                                name="patientId" id="patientId"/>
                    </div>

                    <div class="form-group">
                        <label for="patient_usefrom" class="col-lg-3 col-sm-2 control-label">開始時間: </label>
                        <input type="text" id="patient_usefrom" name="patient_usefrom" class="datepicker form-control required" readonly value="<?php echo $post_data['schedule_start_time']; ?>">
                    </div>

                    <div class="form-group">
                        <label for="patient_useto" class="col-lg-3 col-sm-2 control-label">終了時間: </label>
                        <input type="text" id="patient_useto" name="patient_useto" class="datepicker form-control required" readonly value="<?php echo $post_data['schedule_end_time']; ?>">
                    </div>
                               
                    <div class="form-group">
                        <label for="post_descript" class="col-lg-3 col-sm-2 control-label">申し送り内容: </label>
                        <textarea id="post_descript" name="post_descript" class="form-control"><?php echo $post_data['post_content']; ?></textarea>
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

<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/locales/bootstrap-datepicker.js.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css"/>

<script type="text/javascript">
    // $( function () {
    //   $('.datepicker1').datepicker({
    //     locale: 'ja',
    //     format: 'yyyy-mm-dd',
    //     autoclose: true,
    //   });
    // });
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
        const patientId = $('#patientId').val();
        const postDate = $('#post_date').val();

        if (patientId) {
            $.ajax({
                url: '<?= base_url() ?>post/get_patient_data', // create this endpoint
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
    $('#post_date').on('change', fetchPatientData);
</script>