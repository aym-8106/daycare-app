<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>出退勤ページ</h1>
    </section>

    <section class="content" style="font-size: 18px;">
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <a href="<?php echo base_url(); ?>attendance/list" class="btn btn-primary btn-block btn-flat list-btn" style="font-size: 18px;">出退勤一覧</a>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-12 col-xs-12"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['work_time'] = ($attendance_data['work_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['work_time'];
                    if ($attendance_data['work_time'] != "") {
                ?>
                        <span class="time-text" style="font-size: 18px;">出勤時間：<?php echo $attendance_data['work_time'] ?></span>
                    <?php
                    } else {
                    ?>
                        <button class="btn btn-primary btn-block btn-flat work_btn" style="font-size: 18px;">出勤</button>
                    <?php
                    }
                } else {
                    ?><button class="btn btn-primary btn-block btn-flat work_btn" style="font-size: 18px;">出勤</button>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['leave_time'] = ($attendance_data['leave_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['leave_time'];
                }

                if (!empty($attendance_data) && $attendance_data['work_time'] != "" && $attendance_data['leave_time'] == "") {
                ?>
                    <button class="btn btn-primary btn-block btn-flat leave-btn" style="font-size: 18px;">退勤</button>
                <?php
                } else if (!empty($attendance_data) && $attendance_data['work_time'] != "" && $attendance_data['leave_time'] != "") {
                ?>
                    <span class="time-text" style="font-size: 18px;">退勤時間：<?php echo $attendance_data['leave_time'] ?></span>
                <?php
                } else {
                ?>
                    <button class="btn btn-primary btn-block btn-flat leave-btn" disabled style="font-size: 18px;">退勤</button>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['break_time'] = ($attendance_data['break_time'] == 0) ? '' : $attendance_data['break_time'];
                    $attendance_data['leave_time'] = ($attendance_data['leave_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['leave_time'];
                }

                if (!empty($attendance_data) && $attendance_data['leave_time'] == "") {
                    if (!empty($attendance_data) && $attendance_data['work_time'] != "" && $attendance_data['leave_time'] == "") {
                ?>
                        <button class="btn btn-primary btn-block btn-flat break-start-btn" style="font-size: 18px;">休憩</button>
                    <?php
                    } else if (!empty($attendance_data) && $attendance_data['work_time'] != "" && $attendance_data['break_time'] != "") {
                    ?>
                        <button class="btn btn-primary btn-block btn-flat break-start-btn" style="font-size: 18px;">休憩</button>
                    <?php
                    } else {
                    ?>
                        <button class="btn btn-primary btn-block btn-flat break-start-btn" disabled style="font-size: 18px;">休憩</button>
                    <?php
                    }
                } else if (!empty($attendance_data) && $attendance_data['leave_time'] != "") {
                    ?>
                    <button class="btn btn-primary btn-block btn-flat break-start-btn" style="display: none;font-size: 18px;">休憩</button>
                <?php
                } else {
                ?>
                    <button class="btn btn-primary btn-block btn-flat break-start-btn" disabled style="font-size: 18px;">休憩</button>
                <?php
                }
                ?>

                <button class="btn btn-primary btn-block btn-flat break-end-btn mt-0" style="font-size: 18px;">休憩終了</button>
                <span class="time-text break-time-box" data-staff-id="2" style="font-size: 18px;">休憩時間：
                    <?php if (!empty($attendance_data) && $attendance_data['break_time'] != "") {
                        echo floor($attendance_data['break_time'] / 60) . '分';
                    } ?>
                </span>
                <?php if (!empty($attendance_data['break_time']) && $attendance_data['break_time'] != 0): ?>
                    <span class="time-text final_break-time-box" style="display: block; font-size: 18px;" data-staff-id="2">
                        合計休憩時間：<?php echo floor($attendance_data['break_time'] / 60); ?>分
                    </span>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['leave_time'] = ($attendance_data['leave_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['leave_time'];
                    $attendance_data['overtime_start_time'] = ($attendance_data['overtime_start_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['overtime_start_time'];

                    if (!empty($attendance_data) && $attendance_data['overtime_start_time'] != "") {
                ?>
                        <span class="time-text overtime" style="font-size: 18px;">残業開始：<?php echo $attendance_data['overtime_start_time'] ?></span>
                    <?php
                    } else if (!empty($attendance_data) && $attendance_data['overtime_start_time'] == "") {
                    ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime-btn" style="font-size: 18px;">残業</a>
                    <?php
                    } else {
                    ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime-btn" style="font-size: 18px;">残業</a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="" class="btn btn-primary btn-block btn-flat overtime-btn" style="font-size: 18px;">残業</a>
                <?php
                }
                ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center flex-div">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['leave_time'] = ($attendance_data['leave_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['leave_time'];
                    $attendance_data['overtime_start_time'] = ($attendance_data['overtime_start_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['overtime_start_time'];
                    $attendance_data['overtime_end_time'] = ($attendance_data['overtime_end_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['overtime_end_time'];

                    if (!empty($attendance_data) && $attendance_data['leave_time'] != "" && $attendance_data['overtime_start_time'] != "" && $attendance_data['overtime_end_time'] == "") {
                ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime_break-start-btn" style="font-size: 18px;">残業休憩</a>
                    <?php
                    } else if (!empty($attendance_data) && $attendance_data['work_time'] != "" && $attendance_data['overtime_break_time'] != 0 && $attendance_data['overtime_end_time'] == "") {
                    ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime_break-start-btn" style="font-size: 18px;">残業休憩</a>
                        <span class="time-text overtime_break-time-box" data-staff-id="2" style="font-size: 18px;">
                            合計残業休憩時間：<?php echo floor($attendance_data['overtime_break_time'] / 60); ?>分
                        </span>
                    <?php
                    } else {
                    ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime_break-start-btn" style="display: none;font-size: 18px;">残業休憩</a>
                    <?php
                    }
                } else {
                    ?>
                    <a href="" class="btn btn-primary btn-block btn-flat overtime_break-start-btn" style="display: none;font-size: 18px;">残業休憩</a>
                <?php
                }
                ?>

                <a href="" class="btn btn-primary btn-block btn-flat overtime_break-end-btn mt-0" style="font-size: 18px;">残業休憩終了</a>
                <span class="time-text overtime_break-time-box" data-staff-id="2" style="font-size: 18px;">
                    残業休憩時間：
                    <?php 
                    if (!empty($attendance_data) && $attendance_data['overtime_break_time'] != "") {
                        echo floor($attendance_data['overtime_break_time'] / 60) . '分';
                    } 
                    ?>
                </span>
                <?php if (!empty($attendance_data['overtime_break_time']) && $attendance_data['overtime_break_time'] != 0): ?>
                    <span class="time-text final_overtime_break-time-box" style="display: block; font-size: 18px;" data-staff-id="2">
                        合計残業休憩時間：<?php echo floor($attendance_data['overtime_break_time'] / 60); ?>分
                    </span>
                <?php endif; ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body">
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10 text-center">
                <!-- small box -->
                <?php
                if (!empty($attendance_data)) {
                    $attendance_data['leave_time'] = ($attendance_data['leave_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['leave_time'];
                    $attendance_data['overtime_start_time'] = ($attendance_data['overtime_start_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['overtime_start_time'];
                    $attendance_data['overtime_end_time'] = ($attendance_data['overtime_end_time'] === '0000-00-00 00:00:00') ? '' : $attendance_data['overtime_end_time'];

                    if ($attendance_data['leave_time'] != "" && $attendance_data['overtime_end_time'] != "" && $attendance_data['overtime_start_time'] != "") {
                ?>
                        <span class="time-text" style="font-size: 18px;">残業終了時間：<?php echo $attendance_data['overtime_end_time'] ?></span>
                    <?php
                    } else if ($attendance_data['leave_time'] != "" && $attendance_data['overtime_start_time'] != "") {
                    ?>
                        <a href="" class="btn btn-primary btn-block btn-flat overtime_end_btn" style="font-size: 18px;">残業終了</a>
                    <?php
                    }
                } else {
                    ?>
                    <!-- <a href="" class="btn btn-primary btn-block btn-flat overtime_end_btn ">残業終了</a> -->
                <?php
                }
                ?>
            </div>
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body div-overtime" <?php if(empty($attendance_data['overtime_start_time'])) {?> style="display: none;" <?php } ?> >
            <div class="col-lg-4 col-xs-1"></div>
            <div class="col-lg-4 col-xs-10">
                <form action="<?php echo base_url() ?>attendance/addovertime" method="POST" id="form1" name="form1" class="form-horizontal">
                    <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">残業開始: <span class="important overtime-start">時間を正確に選択してください。</span></label>

                        <div class="col-lg-9 col-sm-10">
                            <input type="text" id="start_time" name="start_time" class="datepicker form-control"<?php if(!empty($attendance_data['overtime_start_time'])) {?>  value="<?php echo date('H:i', strtotime($attendance_data['overtime_start_time'])); ?>" readonly <?php } ?> >
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="start_time" class="col-lg-3 col-sm-2 control-label">理由: <span class="important reason-text">入力してください。</span></label>

                        <div class="col-lg-9 col-sm-10">
                            <textarea id="reason" name="reason" class="datepicker form-control" <?php if(!empty($attendance_data['overtime_start_time'])) {?> readonly <?php } ?> ><?php if(!empty($attendance_data['overtime_start_time'])) { echo $attendance_data['overtime_reason']; } ?></textarea>
                        </div>
                    </div>
                </form>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-1"></div>
        </div>
        <div class="box-body div-overtime" <?php if(empty($attendance_data['overtime_start_time'])) {?> style="display: none;" <?php } ?> >
            <div class="col-lg-4 col-xs-2"></div>
            <div class="col-lg-4 col-xs-8 text-center">
                <!-- small box -->
                <?php if(empty($attendance_data['overtime_start_time'])) {?>
                    <a href="" class="btn btn-primary btn-block btn-flat send_btn">送信</a>
                <?php } ?>
            </div><!-- ./col -->
            <div class="col-lg-4 col-xs-2"></div>
        </div>
    </section>
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/moment-with-locales.min.js"></script>
<script type="text/javascript" src="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>

<script>
    $(document).ready(function() {
        $('.work_btn').on('click', function(e) {
            e.preventDefault();

            const now = new Date();
            const pad = n => n.toString().padStart(2, '0');
            const formattedDate = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            const $button = $(this);

            $.ajax({
                url: '<?= base_url() ?>attendance/insert_work_time',
                type: 'post',
                dataType: 'json',
                data: {
                    work_time: formattedDate
                },
                success: function(response) {
                    if (response.success) {
                        bootoast.toast({
                            message: response.user_name + '様が勤務開始します。',
                            type: 'success',
                            animationDuration: 300,
                        });

                        setTimeout(() => {
                            const $span = $('<span class="time-text" style="font-size: 18px;">').text('出勤時間：' + response.work_time);
                            $button.replaceWith($span);
                            location.reload();
                        }, 600);
                    } else {
                        bootoast.toast({
                            message: response.message,
                            type: 'danger',
                            animationDuration: 300,
                        });
                    }
                },
                error: function() {

                }
            });
        });

        $('.leave-btn').on('click', function(e) {
            e.preventDefault();

            $('.break-start-btn').css('display', 'none');
            $('.break-end-btn').css('display', 'none');
            clearInterval(breakTimer);
            const now = new Date();
            const pad = n => n.toString().padStart(2, '0');
            const formattedDate = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
            const $button = $(this);

            $.ajax({
                url: '<?= base_url() ?>attendance/update_leave_time',
                type: 'post',
                dataType: 'json',
                data: {
                    leave_time: formattedDate
                },
                success: function(response) {
                    if (response.success) {
                        bootoast.toast({
                            message: response.user_name + '様が勤務を終了します。',
                            type: 'success',
                            animationDuration: 300,
                        });

                        setTimeout(() => {
                            const $span = $('<span class="time-text" style="font-size: 18px;">').text('退勤時間：' + response.leave_time);
                            $button.replaceWith($span);
                            location.reload();
                        }, 600);
                    } else {

                    }

                },
                error: function() {

                }
            });
        });

        let breakTimer;
        let secondsElapsed;

        function startBreakTimer() {
            breakTimer = setInterval(function() {
                secondsElapsed++; // Make sure you increment the counter
                $.ajax({
                    url: '<?= base_url() ?>attendance/end_break_time',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        break_duration: secondsElapsed
                    },
                    success: function(response) {
                        if (response.success) {
                            if (secondsElapsed < 3600) {
                                let minutes = Math.floor(secondsElapsed / 60);
                                $('.break-time-box').text('休憩時間：' + minutes + '分');
                            } else {
                                clearInterval(breakTimer); // Stop the interval
                                $('.break-start-btn').hide();
                                $('.break-end-btn').hide();
                                let minutes = Math.floor(secondsElapsed / 60);
                                $('.break-time-box').text('合計休憩時間：' + minutes + '分');
                            }
                        } else {
                            bootoast.toast({
                                message: 'Failed to save break time.',
                                type: 'danger',
                                animationDuration: 300,
                            });
                        }
                    },
                    error: function() {
                        bootoast.toast({
                            message: 'Error connecting to server.',
                            type: 'danger',
                            animationDuration: 300,
                        });
                    }
                });
            }, 1000);
        }

        // Check if there's an ongoing break from localStorage on page load
        $(document).ready(function() {
            const breakStartTime = localStorage.getItem('breakStartTime');
            if (breakStartTime) {
                const now = Date.now();
                const elapsedSinceStart = Math.floor((now - parseInt(breakStartTime)) / 1000);

                const today = new Date();
                const pad = n => n.toString().padStart(2, '0');
                const formattedDate = `${today.getFullYear()}-${pad(today.getMonth() + 1)}-${pad(today.getDate())}`;

                $.ajax({
                    url: '<?= base_url() ?>attendance/get_break_time',
                    type: 'POST',
                    data: {
                        work_date: formattedDate
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.break_time != null) {
                            secondsElapsed = parseInt(response.break_time);
                            console.log(elapsedSinceStart);
                            console.log(response.break_time);
                            if (secondsElapsed < 3600) {
                                $('.break-start-btn').hide();
                                $('.break-end-btn').show();
                                $('.break-time-box').show().text('休憩時間：' + Math.floor(secondsElapsed / 60) + '分');
                                $('.final_break-time-box').hide();
                                startBreakTimer();
                            } else {
                                $('.break-start-btn').hide();
                                $('.break-end-btn').hide();
                                $('.break-time-box').text('合計休憩時間：' + Math.floor(secondsElapsed / 60) + '分');
                            }
                        }
                    }
                });
            }
        });

        $('.break-start-btn').on('click', function(e) {
            e.preventDefault();

            $('.break-start-btn').hide();
            $('.break-end-btn').show();
            $('.break-time-box').show();
            $('.final_break-time-box').hide();

            const now = new Date();
            const pad = n => n.toString().padStart(2, '0');
            const formattedDate = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

            clearInterval(breakTimer);

            $.ajax({
                url: '<?= base_url() ?>attendance/get_break_time',
                type: 'POST',
                data: {
                    work_date: formattedDate
                },
                dataType: 'json',
                success: function(response) {
                    bootoast.toast({
                        message: response.user_name + '様が休憩を開始します。',
                        type: 'info',
                        animationDuration: 300,
                    });
                    secondsElapsed = response.break_time || 0;
                    $('.break-time-box').show().text('休憩時間：' + Math.floor(secondsElapsed / 60) + '分');
                    // Save the current timestamp to localStorage
                    localStorage.setItem('breakStartTime', Date.now());

                    startBreakTimer();
                }
            });
        });

        $('.break-end-btn').on('click', function(e) {
            e.preventDefault();

            clearInterval(breakTimer);
            localStorage.removeItem('breakStartTime');

            $('.break-end-btn').hide();
            $('.break-start-btn').show();

            $.ajax({
                url: '<?= base_url() ?>attendance/end_break_time',
                type: 'POST',
                dataType: 'json',
                data: {
                    break_duration: secondsElapsed
                },
                success: function(response) {
                    if (response.success) {
                        bootoast.toast({
                            message: '休憩を終了します。',
                            type: 'success',
                            animationDuration: 300,
                        });

                        setTimeout(() => {
                            $('.break-time-box').text('合計休憩時間：' + Math.floor(secondsElapsed / 60) + '分');
                            location.reload();
                        }, 600);
                    } else {
                        bootoast.toast({
                            message: 'Failed to save break time.',
                            type: 'danger',
                            animationDuration: 300,
                        });
                        location.reload();
                    }
                },
                error: function() {
                    bootoast.toast({
                        message: 'エラーが発生しました。',
                        type: 'danger',
                        animationDuration: 300,
                    });
                }
            });
        });

        let breakTimer1;
        let secondsElapsed1 = 0;

        $(document).ready(function() {
            const savedStart = localStorage.getItem('overtimeBreakStartTime');
            const savedElapsed = localStorage.getItem('overtimeBreakElapsed');

            if (savedStart && savedElapsed) {
                secondsElapsed1 = parseInt(savedElapsed);
                $('.overtime_break-start-btn').hide();
                $('.overtime_break-end-btn').show();
                $('.overtime_break-time-box').show();
                $('.final_overtime_break-time-box').hide();
                startOvertimeBreakTimer(); // Start counting
            }
        });

        // 休憩開始ボタン
        $('.overtime_break-start-btn').on('click', function(e) {
            e.preventDefault();

            const now = new Date();
            const overtimeRaw = $('.overtime').text().trim();
            const overtimeParts = overtimeRaw.split('：');
            const overtimeDateTimeStr = overtimeParts[overtimeParts.length - 1];
            const overtimeTime = new Date(overtimeDateTimeStr);

            if (now < overtimeTime) {
                bootoast.toast({
                    message: '現在の時刻が残業開始時刻より前のため、休憩できません。',
                    type: 'warning',
                    animationDuration: 300,
                });
                return;
            }

            $('.overtime_break-start-btn').hide();
            $('.overtime_break-end-btn').show();
            $('.overtime_break-time-box').show();
            $('.final_overtime_break-time-box').hide();

            const pad = n => n.toString().padStart(2, '0');
            const formattedDate = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}`;

            $.ajax({
                url: '<?= base_url() ?>attendance/get_overtime_break_time',
                type: 'POST',
                data: {
                    work_date: formattedDate
                },
                dataType: 'json',
                success: function(response) {
                    bootoast.toast({
                        message: response.user_name + '様が残業休憩を開始します。',
                        type: 'info',
                        animationDuration: 300,
                    });

                    secondsElapsed1 = parseInt(response.overtime_break_time) || 0;

                    // Save current time + already elapsed in localStorage
                    localStorage.setItem('overtimeBreakStartTime', new Date().toISOString());
                    localStorage.setItem('overtimeBreakElapsed', secondsElapsed1);

                    startOvertimeBreakTimer();
                }
            });
        });

        function startOvertimeBreakTimer() {
            clearInterval(breakTimer1);

            breakTimer1 = setInterval(function() {
                secondsElapsed1++;
                $('.overtime_break-time-box').text('残業休憩時間：' + Math.floor(secondsElapsed1 / 60) + '分');

                localStorage.setItem('overtimeBreakElapsed', secondsElapsed1); // persist elapsed seconds

                $.ajax({
                    url: '<?= base_url() ?>attendance/overtime_end_break_time',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        break_duration1: secondsElapsed1
                    }
                });
            }, 1000);
        }

        // 休憩終了ボタン
        $('.overtime_break-end-btn').on('click', function(e) {
            e.preventDefault();

            clearInterval(breakTimer1);

            // Remove storage info
            localStorage.removeItem('overtimeBreakStartTime');
            localStorage.removeItem('overtimeBreakElapsed');

            $('.overtime_break-end-btn').hide();
            $('.overtime_break-start-btn').show();

            $.ajax({
                url: '<?= base_url() ?>attendance/overtime_end_break_time',
                type: 'POST',
                dataType: 'json',
                data: {
                    break_duration1: secondsElapsed1
                },
                success: function(response) {
                    bootoast.toast({
                        message: '残業休憩を終了します。',
                        type: 'success',
                        animationDuration: 300,
                    });

                    setTimeout(() => {
                        $('.overtime_break-time-box').text('合計残業休憩時間：' + Math.floor(secondsElapsed1 / 60) + '分');
                        location.reload();
                    }, 600);
                }
            });
        });

        $('.overtime_end_btn').on('click', function(e) {
            e.preventDefault();

            const now = new Date();

            const overtimeRaw = $('.overtime').text().trim();
            const overtimeParts = overtimeRaw.split('：');
            const overtimeDateTimeStr = overtimeParts[overtimeParts.length - 1];
            const overtimeTime = new Date(overtimeDateTimeStr);
            if (now < overtimeTime) {
                bootoast.toast({
                    message: '現在の時刻が残業開始時刻より前のため、終了できません。',
                    type: 'warning',
                    animationDuration: 300,
                });
                return;
            }
            clearInterval(breakTimer1);
            $('.overtime_break-end-btn').css('display', 'none');
            $('.overtime_break-start-btn').css('display', 'none');
            $('.overtime_break-time-box').css('display', 'none');
            $('.final_overtime_break-time-box').css('display', 'block');

            const pad = n => n.toString().padStart(2, '0');
            const formattedDate = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())} ${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;

            const $button = $(this);

            $.ajax({
                url: '<?= base_url() ?>attendance/update_overtime_end_time',
                type: 'post',
                dataType: 'json',
                data: {
                    overtime_end_time: formattedDate
                },
                success: function(response) {
                    if (response.success) {
                        const $span = $('<span class="time-text" style="font-size: 18px;">').text('残業終了時間：' + response.overtime_end_time);
                        $button.replaceWith($span);
                        bootoast.toast({
                            message: '残業終了しました。',
                            type: 'success',
                            animationDuration: 300,
                        });

                        setTimeout(() => {
                            location.reload();
                        }, 600);
                    }
                },
                error: function() {
                    bootoast.toast({
                        message: 'エラーが発生しました。',
                        type: 'danger',
                        animationDuration: 300,
                    });
                }
            });
        });
        
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
        $('.overtime-btn').on('click', function(e) {
            e.preventDefault();
            $('.div-overtime').slideDown();
        });
    });



</script>