<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>残業申請ページ</h1>
    </section>
    
    <section class="content">
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10">
                <form action="<?php echo base_url() ?>attendance/addovertime" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group text-center">
                        <label class="col-lg-13 col-sm-12"><?php echo (date('n月j日'))?></label>
                    </div>
                    <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">残業開始: <span class="important overtime-start">時間を正確に選択してください。</span></label>

                        <div class="col-lg-9 col-sm-10">
                            <input type="text" id="start_time" name="start_time" class="datepicker form-control" value="<?php echo $start_time; ?>">
                        </div>
                    </div>

                    <!-- <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">残業終了: </label>

                        <div class="col-lg-9 col-sm-10">
                            <input type="text" id="end_time" name="end_time" class="datepicker form-control" value="<?php //echo $end_time; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">休憩時間: </label>

                        <div class="col-lg-9 col-sm-10">
                            <input type="text" id="relax_time" name="relax_time" class="datepicker form-control" value="<?php //echo $relax_time; ?>">
                        </div>
                    </div> -->
                               
                    <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">理由: <span class="important reason-text">入力してください。</span></label>

                        <div class="col-lg-9 col-sm-10">
                            <textarea id="reason" name="reason" class="datepicker form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <!-- small box -->
                <a href="" class="btn btn-primary btn-block btn-flat send_btn">送信</a>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-2"></div>
        </div>
    </section>
</div>

<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>-->
<!--<script type="text/javascript" src="--><?php //echo base_url(); ?><!--assets/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.ja.min.js"></script>-->
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script type="text/javascript">
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

    $('.send_btn').on('click', function(e) {
        e.preventDefault();

        var overtime_start = $('#start_time').val();
        var reason = $('#reason').val();

        const now = new Date();
        const pad = n => n.toString().padStart(2, '0');
        const now_time = `${pad(now.getHours())}:${pad(now.getMinutes())}`;
        const today = new Date().toISOString().split('T')[0];

        const overtimeDate = new Date(`${today}T${overtime_start}`);
        const nowtimeDate = new Date(`${today}T${now_time}`);
        const diffInSeconds = (overtimeDate - nowtimeDate) / 1000;

        let valid = true;

        if (diffInSeconds <= 0) {
            $('.overtime-start').css('display', 'block');
            valid = false;
        } else {
            $('.overtime-start').css('display', 'none');
        }

        if (reason == "") {
            $('.reason-text').css('display', 'block');
            valid = false;
        } else {
            $('.reason-text').css('display', 'none');
        }

        if (valid) {
            $.ajax({
                url: '<?= base_url() ?>attendance/insert_reason/',
                type: 'post',
                dataType: 'json',
                data: {
                    reason_time: overtime_start,
                    reason_text: reason
                },
                success: function (response) {
                    if (response.success) {
                        bootoast.toast({
                            message: response.over_time + '時から残業を開始する必要があります。',
                            type:'success',
                            animationDuration: 300,
                        });

                        setTimeout(() => {
                            window.location.href = '<?= base_url() ?>attendance/index';
                        }, 450);
                    } else {
                    
                    }
                },
                error: function () {

                }
            });
        }
    });
</script>
