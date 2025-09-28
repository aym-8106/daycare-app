<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> 管理者出退勤
            <small>出勤・退勤の打刻</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-user"></i> <?php echo escape_html($staff_name); ?>さんの出退勤
                        </h3>
                    </div>
                    <div class="box-body text-center">
                        <div class="row">
                            <div class="col-md-12">
                                <h4><?php echo date('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][date('w')] . '）'); ?></h4>
                                <!-- デバッグ情報 -->
                                <div style="font-size: 12px; color: #666; margin: 10px 0;">
                                    デバッグ: has_checked_in=<?php echo $has_checked_in ? 'true' : 'false'; ?>,
                                    has_checked_out=<?php echo $has_checked_out ? 'true' : 'false'; ?>
                                    <?php if($attendance): ?>
                                    <br>work_time=<?php echo $attendance['work_time']; ?>,
                                    leave_time=<?php echo $attendance['leave_time']; ?>
                                    <?php endif; ?>
                                </div>
                                <hr>
                            </div>
                        </div>

                        <!-- 出勤状況表示 -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-box <?php echo $has_checked_in ? 'bg-green' : 'bg-gray'; ?>">
                                    <span class="info-box-icon"><i class="fa fa-sign-in"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">出勤時刻</span>
                                        <span class="info-box-number" id="work-time">
                                            <?php
                                            if ($has_checked_in) {
                                                echo date('H:i', strtotime($attendance['work_time']));
                                            } else {
                                                echo '--:--';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-box <?php echo $has_checked_out ? 'bg-red' : 'bg-gray'; ?>">
                                    <span class="info-box-icon"><i class="fa fa-sign-out"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">退勤時刻</span>
                                        <span class="info-box-number" id="leave-time">
                                            <?php
                                            if ($has_checked_out) {
                                                echo date('H:i', strtotime($attendance['leave_time']));
                                            } else {
                                                echo '--:--';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- 打刻ボタン -->
                        <div class="row">
                            <div class="col-md-6">
                                <button type="button"
                                        class="btn btn-success btn-lg btn-block"
                                        id="check-in-btn"
                                        <?php echo $has_checked_in ? 'disabled' : ''; ?>>
                                    <i class="fa fa-sign-in"></i><br>
                                    <?php echo $has_checked_in ? '出勤済み' : '出勤'; ?>
                                </button>
                            </div>
                            <div class="col-md-6">
                                <button type="button"
                                        class="btn <?php echo (!$has_checked_in || $has_checked_out) ? 'btn-default' : 'btn-danger'; ?> btn-lg btn-block"
                                        id="check-out-btn"
                                        <?php
                                        $should_disable = (!$has_checked_in || $has_checked_out);
                                        echo $should_disable ? 'disabled="disabled"' : '';
                                        ?>>
                                    <i class="fa fa-sign-out"></i><br>
                                    <?php echo $has_checked_out ? '退勤済み' : '退勤'; ?>
                                </button>
                                <!-- デバッグ情報 -->
                                <div style="font-size: 10px; color: #999; margin-top: 5px;">
                                    退勤ボタン無効条件: (!<?php echo $has_checked_in ? 'true' : 'false'; ?> || <?php echo $has_checked_out ? 'true' : 'false'; ?>) = <?php echo $should_disable ? 'true' : 'false'; ?>
                                </div>
                            </div>
                        </div>

                        <div class="row" style="margin-top: 20px;">
                            <div class="col-md-12">
                                <p class="text-muted">
                                    <i class="fa fa-info-circle"></i>
                                    管理者として出退勤を記録します
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // 出勤ボタン
    $('#check-in-btn').click(function() {
        if ($(this).prop('disabled')) return;

        $.post('<?php echo base_url('admin/adminattendance/check_in'); ?>', {}, function(response) {
            console.log('出勤レスポンス:', response);
            if (response.success) {
                alert(response.message);
                // 出勤時刻を更新
                $('#work-time').text(response.time);

                // 出勤ボタンを無効化
                $('#check-in-btn')
                    .html('<i class="fa fa-sign-in"></i><br>出勤済み')
                    .prop('disabled', true)
                    .removeClass('btn-success')
                    .addClass('btn-default');

                // 退勤ボタンを有効化
                console.log('退勤ボタン有効化前の状態:', $('#check-out-btn').prop('disabled'));
                $('#check-out-btn')
                    .prop('disabled', false)
                    .removeAttr('disabled')
                    .removeClass('btn-default')
                    .addClass('btn-danger')
                    .html('<i class="fa fa-sign-out"></i><br>退勤');
                console.log('退勤ボタン有効化後の状態:', $('#check-out-btn').prop('disabled'));

                // 出勤状況の表示を更新
                $('.info-box:first').removeClass('bg-gray').addClass('bg-green');
            } else {
                alert('エラー: ' + response.message);
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('通信エラー:', xhr.responseText);
            alert('通信エラーが発生しました。');
        });
    });

    // 退勤ボタン
    $('#check-out-btn').click(function() {
        if ($(this).prop('disabled')) {
            console.log('退勤ボタンが無効です');
            return;
        }

        console.log('退勤打刻開始');
        $.post('<?php echo base_url('admin/adminattendance/check_out'); ?>', {}, function(response) {
            console.log('退勤レスポンス:', response);
            if (response.success) {
                alert(response.message);
                // 退勤時刻を更新
                $('#leave-time').text(response.time);

                // 退勤ボタンを無効化
                $('#check-out-btn')
                    .html('<i class="fa fa-sign-out"></i><br>退勤済み')
                    .prop('disabled', true)
                    .removeClass('btn-danger')
                    .addClass('btn-default');

                // 退勤状況の表示を更新
                $('.info-box:last').removeClass('bg-gray').addClass('bg-red');
            } else {
                console.log('退勤エラー:', response.message);
                alert('エラー: ' + response.message);
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('退勤通信エラー:', xhr.responseText);
            alert('通信エラーが発生しました。');
        });
    });
});
</script>