<div class="content-wrapper">
    <section class="content-header">
        <h1>決済履歴<small>過去の決済記録</small></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">決済履歴一覧</h3>
                    </div>
                    <div class="box-body">
                        <?php if (empty($payments)): ?>
                            <p class="text-center text-muted">決済履歴がありません。</p>
                        <?php else: ?>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>決済日</th>
                                        <th>プラン</th>
                                        <th>金額</th>
                                        <th>ステータス</th>
                                        <th>請求書ID</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?php echo date('Y年m月d日', strtotime($payment['payment_date'])); ?></td>
                                        <td><?php echo htmlspecialchars($payment['plan_name'] ?? '-'); ?></td>
                                        <td>¥<?php echo number_format($payment['amount']); ?></td>
                                        <td>
                                            <?php
                                            $status_class = $payment['status'] === 'succeeded' ? 'success' : 'danger';
                                            $status_text = $payment['status'] === 'succeeded' ? '成功' : '失敗';
                                            ?>
                                            <span class="label label-<?php echo $status_class; ?>">
                                                <?php echo $status_text; ?>
                                            </span>
                                        </td>
                                        <td><small><?php echo htmlspecialchars($payment['stripe_invoice_id'] ?? '-'); ?></small></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>