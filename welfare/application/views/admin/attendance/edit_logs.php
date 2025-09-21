<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            <i class="fa fa-history"></i> 出退勤編集ログ
            <small>編集履歴の確認</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-xs-12">
                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title">編集ログ一覧</h3>
                        <div class="box-tools">
                            <a href="<?php echo admin_url('attendance'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> 出退勤管理に戻る
                            </a>
                        </div>
                    </div>

                    <div class="box-body">
                        <?php if (!empty($edit_logs)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>編集日時</th>
                                            <th>スタッフ名</th>
                                            <th>勤務日</th>
                                            <th>編集項目</th>
                                            <th>変更前</th>
                                            <th>変更後</th>
                                            <th>編集者</th>
                                            <th>編集理由</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($edit_logs as $log): ?>
                                            <tr>
                                                <td><?php echo date('Y-m-d H:i:s', strtotime($log['edited_at'])); ?></td>
                                                <td><?php echo $log['staff_name']; ?></td>
                                                <td><?php echo date('Y-m-d', strtotime($log['work_date'])); ?></td>
                                                <td>
                                                    <span class="label label-info"><?php echo $log['field_name']; ?></span>
                                                </td>
                                                <td>
                                                    <?php if ($log['old_value']): ?>
                                                        <span class="text-danger"><?php echo $log['old_value']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">（空）</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($log['new_value']): ?>
                                                        <span class="text-success"><?php echo $log['new_value']; ?></span>
                                                    <?php else: ?>
                                                        <span class="text-muted">（空）</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>管理者ID: <?php echo $log['edited_by']; ?></td>
                                                <td>
                                                    <?php if ($log['edit_reason']): ?>
                                                        <small><?php echo nl2br(htmlspecialchars($log['edit_reason'])); ?></small>
                                                    <?php else: ?>
                                                        <span class="text-muted">理由なし</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="fa fa-info-circle"></i> 編集ログはありません。
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>