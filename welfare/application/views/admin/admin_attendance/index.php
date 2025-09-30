<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-clock-o"></i> 出退勤打刻
            <small>事業所の全職員</small>
        </h1>
    </section>

    <section class="content">
        <!-- 日付表示 -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-solid">
                    <div class="box-body text-center" style="background-color: #f8f9fa; padding: 20px;">
                        <h2 style="margin: 0; font-weight: bold; color: #333;">
                            <i class="fa fa-calendar"></i>
                            <?php echo date('Y年m月d日（' . ['日', '月', '火', '水', '木', '金', '土'][date('w')] . '）'); ?>
                        </h2>
                        <p style="margin: 10px 0 0 0; font-size: 24px; color: #666;" id="current-time"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 職員一覧 -->
        <div class="row">
            <?php foreach($staff_list as $staff): ?>
            <div class="col-lg-4 col-md-6 col-sm-12">
                <div class="box box-widget widget-user-2">
                    <div class="widget-user-header" style="background-color: <?php echo $staff['has_checked_out'] ? '#d9534f' : ($staff['has_checked_in'] ? '#5cb85c' : '#777'); ?>; padding: 15px;">
                        <div class="widget-user-image">
                            <i class="fa fa-user-circle" style="font-size: 48px; color: white;"></i>
                        </div>
                        <h3 class="widget-user-username" style="margin-left: 60px; color: white; font-size: 20px; font-weight: bold;">
                            <?php echo escape_html($staff['staff_name']); ?>
                        </h3>
                        <h5 class="widget-user-desc" style="margin-left: 60px; color: rgba(255,255,255,0.8);">
                            <?php
                            if ($staff['has_checked_out']) {
                                echo '<i class="fa fa-check-circle"></i> 退勤済み';
                            } elseif ($staff['has_checked_in']) {
                                echo '<i class="fa fa-check"></i> 出勤中';
                            } else {
                                echo '<i class="fa fa-circle-o"></i> 未出勤';
                            }
                            ?>
                        </h5>
                    </div>
                    <div class="box-footer no-padding">
                        <ul class="nav nav-stacked">
                            <li style="padding: 10px 15px; border-bottom: 1px solid #f4f4f4;">
                                <span style="font-weight: bold;"><i class="fa fa-sign-in text-green"></i> 出勤時刻:</span>
                                <span class="pull-right" style="font-size: 18px; font-weight: bold;" id="work-time-<?php echo $staff['staff_id']; ?>">
                                    <?php echo $staff['work_time'] ? $staff['work_time'] : '<span style="color: #999;">--:--</span>'; ?>
                                </span>
                            </li>
                            <li style="padding: 10px 15px;">
                                <span style="font-weight: bold;"><i class="fa fa-sign-out text-red"></i> 退勤時刻:</span>
                                <span class="pull-right" style="font-size: 18px; font-weight: bold;" id="leave-time-<?php echo $staff['staff_id']; ?>">
                                    <?php echo $staff['leave_time'] ? $staff['leave_time'] : '<span style="color: #999;">--:--</span>'; ?>
                                </span>
                            </li>
                        </ul>
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-xs-6">
                                <button type="button"
                                        class="btn btn-success btn-block btn-lg check-in-btn"
                                        data-staff-id="<?php echo $staff['staff_id']; ?>"
                                        data-staff-name="<?php echo escape_html($staff['staff_name']); ?>"
                                        <?php echo $staff['has_checked_in'] ? 'disabled' : ''; ?>
                                        style="font-size: 18px; padding: 15px;">
                                    <i class="fa fa-sign-in"></i>
                                    <?php echo $staff['has_checked_in'] ? '出勤済み' : '出勤'; ?>
                                </button>
                            </div>
                            <div class="col-xs-6">
                                <button type="button"
                                        class="btn btn-danger btn-block btn-lg check-out-btn"
                                        data-staff-id="<?php echo $staff['staff_id']; ?>"
                                        data-staff-name="<?php echo escape_html($staff['staff_name']); ?>"
                                        <?php echo (!$staff['has_checked_in'] || $staff['has_checked_out']) ? 'disabled' : ''; ?>
                                        style="font-size: 18px; padding: 15px;">
                                    <i class="fa fa-sign-out"></i>
                                    <?php echo $staff['has_checked_out'] ? '退勤済み' : '退勤'; ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (empty($staff_list)): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info text-center">
                    <i class="fa fa-info-circle"></i> 登録されている職員がいません。
                </div>
            </div>
        </div>
        <?php endif; ?>
    </section>
</div>

<style>
.widget-user-2 .widget-user-header {
    transition: background-color 0.3s;
}
.box-widget {
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}
.box-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}
.btn-lg {
    transition: all 0.2s;
}
.btn-lg:not(:disabled):hover {
    transform: scale(1.05);
}
.btn-lg:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>

<script src="<?php echo base_url(); ?>assets/bower_components/jquery/dist/jquery.min.js"></script>
<script>
$(document).ready(function() {
    // 現在時刻の表示を更新
    function updateTime() {
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var seconds = String(now.getSeconds()).padStart(2, '0');
        $('#current-time').text(hours + ':' + minutes + ':' + seconds);
    }
    updateTime();
    setInterval(updateTime, 1000);

    // 出勤ボタン
    $('.check-in-btn').click(function() {
        if ($(this).prop('disabled')) return;

        var staffId = $(this).data('staff-id');
        var staffName = $(this).data('staff-name');
        var btn = $(this);
        var card = btn.closest('.box-widget');

        if (!confirm(staffName + 'さんの出勤を打刻しますか？')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 処理中...');

        $.post('<?php echo base_url('admin/adminattendance/check_in'); ?>', {
            staff_id: staffId
        }, function(response) {
            if (response.success) {
                // 成功メッセージ
                alert(staffName + 'さんの出勤打刻が完了しました。\n時刻: ' + response.time);

                // 出勤時刻を更新
                $('#work-time-' + staffId).html(response.time);

                // 出勤ボタンを無効化
                btn.html('<i class="fa fa-sign-in"></i> 出勤済み');

                // 退勤ボタンを有効化
                var checkOutBtn = card.find('.check-out-btn');
                checkOutBtn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> 退勤');

                // ヘッダーの色を変更
                card.find('.widget-user-header')
                    .css('background-color', '#5cb85c')
                    .find('.widget-user-desc')
                    .html('<i class="fa fa-check"></i> 出勤中');
            } else {
                alert('エラー: ' + response.message);
                btn.prop('disabled', false).html('<i class="fa fa-sign-in"></i> 出勤');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('通信エラー:', xhr.responseText);
            alert('通信エラーが発生しました。');
            btn.prop('disabled', false).html('<i class="fa fa-sign-in"></i> 出勤');
        });
    });

    // 退勤ボタン
    $('.check-out-btn').click(function() {
        if ($(this).prop('disabled')) return;

        var staffId = $(this).data('staff-id');
        var staffName = $(this).data('staff-name');
        var btn = $(this);
        var card = btn.closest('.box-widget');

        if (!confirm(staffName + 'さんの退勤を打刻しますか？')) {
            return;
        }

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> 処理中...');

        $.post('<?php echo base_url('admin/adminattendance/check_out'); ?>', {
            staff_id: staffId
        }, function(response) {
            if (response.success) {
                // 成功メッセージ
                alert(staffName + 'さんの退勤打刻が完了しました。\n時刻: ' + response.time);

                // 退勤時刻を更新
                $('#leave-time-' + staffId).html(response.time);

                // 退勤ボタンを無効化
                btn.html('<i class="fa fa-sign-out"></i> 退勤済み');

                // ヘッダーの色を変更
                card.find('.widget-user-header')
                    .css('background-color', '#d9534f')
                    .find('.widget-user-desc')
                    .html('<i class="fa fa-check-circle"></i> 退勤済み');
            } else {
                alert('エラー: ' + response.message);
                btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> 退勤');
            }
        }, 'json').fail(function(xhr, status, error) {
            console.log('通信エラー:', xhr.responseText);
            alert('通信エラーが発生しました。');
            btn.prop('disabled', false).html('<i class="fa fa-sign-out"></i> 退勤');
        });
    });
});
</script>