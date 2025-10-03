<style>
/* 決済履歴画面カスタムスタイル */
.payment-history-table {
    background: white;
    border-radius: 4px;
    overflow: hidden;
}

.payment-history-table thead {
    background-color: #f8f9fa;
}

.payment-history-table thead th {
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 10px;
}

.payment-history-table tbody tr {
    transition: background-color 0.2s;
}

.payment-history-table tbody tr:hover {
    background-color: #f8f9fa;
}

.payment-history-table td {
    vertical-align: middle;
    padding: 12px 10px;
}

.payment-amount {
    font-weight: 600;
    font-size: 15px;
    color: #28a745;
}

.payment-date {
    font-size: 14px;
    color: #495057;
}

.invoice-id {
    font-family: 'Courier New', monospace;
    font-size: 11px;
    color: #6c757d;
}

/* ステータスラベルのカスタマイズ */
.label {
    font-size: 12px;
    padding: 5px 10px;
    border-radius: 3px;
}

.label-success {
    background-color: #28a745;
}

.label-danger {
    background-color: #dc3545;
}

.label-warning {
    background-color: #ffc107;
    color: #212529;
}

.label-info {
    background-color: #17a2b8;
}

/* 空データメッセージ */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6c757d;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
    opacity: 0.5;
}

/* ページネーションのカスタマイズ */
.pagination > li > a,
.pagination > li > span {
    color: #667eea;
    border-color: #dee2e6;
}

.pagination > li > a:hover,
.pagination > li > span:hover {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #667eea;
}

.pagination > .active > a,
.pagination > .active > span {
    background-color: #667eea;
    border-color: #667eea;
}

.pagination > .active > a:hover,
.pagination > .active > span:hover {
    background-color: #5568d3;
    border-color: #5568d3;
}

.pagination > .disabled > span,
.pagination > .disabled > a {
    color: #6c757d;
    background-color: #fff;
    border-color: #dee2e6;
}

/* モバイル対応 */
@media (max-width: 768px) {
    .payment-history-table {
        font-size: 13px;
    }

    .payment-history-table td,
    .payment-history-table th {
        padding: 8px 5px;
    }

    .invoice-id {
        font-size: 10px;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-history"></i>
            決済履歴
            <small>過去の決済記録</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo site_url('company/payment'); ?>"><i class="fa fa-credit-card"></i> 料金プラン</a></li>
            <li class="active">決済履歴</li>
        </ol>
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
                            <div class="empty-state">
                                <i class="fa fa-inbox"></i>
                                <p>決済履歴がありません。</p>
                                <a href="<?php echo site_url('company/payment'); ?>" class="btn btn-primary">
                                    <i class="fa fa-credit-card"></i> プランを選択
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped payment-history-table">
                                    <thead>
                                        <tr>
                                            <th><i class="fa fa-calendar"></i> 決済日</th>
                                            <th><i class="fa fa-tag"></i> プラン</th>
                                            <th><i class="fa fa-jpy"></i> 金額</th>
                                            <th><i class="fa fa-check-circle"></i> ステータス</th>
                                            <th><i class="fa fa-file-text-o"></i> 請求書ID</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($payments as $payment): ?>
                                        <tr>
                                            <td class="payment-date">
                                                <?php echo date('Y年m月d日', strtotime($payment['payment_date'])); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($payment['plan_name'] ?? '-'); ?></td>
                                            <td class="payment-amount">¥<?php echo number_format($payment['amount']); ?></td>
                                            <td>
                                                <?php
                                                $status_map = [
                                                    'succeeded' => ['class' => 'success', 'text' => '成功'],
                                                    'failed' => ['class' => 'danger', 'text' => '失敗'],
                                                    'pending' => ['class' => 'warning', 'text' => '処理中'],
                                                    'refunded' => ['class' => 'info', 'text' => '返金済み']
                                                ];
                                                $status = $status_map[$payment['status']] ?? ['class' => 'default', 'text' => $payment['status']];
                                                ?>
                                                <span class="label label-<?php echo $status['class']; ?>">
                                                    <?php echo $status['text']; ?>
                                                </span>
                                            </td>
                                            <td class="invoice-id"><?php echo htmlspecialchars($payment['stripe_invoice_id'] ?? '-'); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- ページネーション -->
                            <?php if ($total_pages > 1): ?>
                            <div class="box-footer clearfix">
                                <ul class="pagination pagination-sm no-margin pull-right">
                                    <!-- 前へボタン -->
                                    <?php if ($current_page > 1): ?>
                                    <li>
                                        <a href="<?php echo site_url('company/payment-history?offset=' . (($current_page - 2) * $limit)); ?>">
                                            &laquo; 前へ
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li class="disabled"><span>&laquo; 前へ</span></li>
                                    <?php endif; ?>

                                    <!-- ページ番号 -->
                                    <?php
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);

                                    if ($start_page > 1): ?>
                                        <li><a href="<?php echo site_url('company/payment-history?offset=0'); ?>">1</a></li>
                                        <?php if ($start_page > 2): ?>
                                            <li class="disabled"><span>...</span></li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <?php if ($i == $current_page): ?>
                                            <li class="active"><span><?php echo $i; ?></span></li>
                                        <?php else: ?>
                                            <li>
                                                <a href="<?php echo site_url('company/payment-history?offset=' . (($i - 1) * $limit)); ?>">
                                                    <?php echo $i; ?>
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endfor; ?>

                                    <?php if ($end_page < $total_pages): ?>
                                        <?php if ($end_page < $total_pages - 1): ?>
                                            <li class="disabled"><span>...</span></li>
                                        <?php endif; ?>
                                        <li>
                                            <a href="<?php echo site_url('company/payment-history?offset=' . (($total_pages - 1) * $limit)); ?>">
                                                <?php echo $total_pages; ?>
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <!-- 次へボタン -->
                                    <?php if ($current_page < $total_pages): ?>
                                    <li>
                                        <a href="<?php echo site_url('company/payment-history?offset=' . ($current_page * $limit)); ?>">
                                            次へ &raquo;
                                        </a>
                                    </li>
                                    <?php else: ?>
                                    <li class="disabled"><span>次へ &raquo;</span></li>
                                    <?php endif; ?>
                                </ul>

                                <div class="pull-left">
                                    <small class="text-muted">
                                        全 <?php echo $total_count; ?> 件中
                                        <?php echo ($offset + 1); ?> - <?php echo min($offset + $limit, $total_count); ?> 件目を表示
                                    </small>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>