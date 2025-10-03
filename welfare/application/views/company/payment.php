<style>
/* 決済画面カスタムスタイル */
.subscription-info-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.subscription-info-card h3 {
    color: white;
    margin-top: 0;
    font-weight: 600;
}

.subscription-status {
    display: inline-block;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    margin-left: 10px;
}

.subscription-status.active {
    background-color: #28a745;
    color: white;
}

.subscription-status.inactive {
    background-color: #6c757d;
    color: white;
}

.subscription-status.past_due {
    background-color: #ffc107;
    color: #333;
}

.subscription-info-row {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.subscription-info-item {
    flex: 1;
}

.subscription-info-item label {
    display: block;
    font-size: 12px;
    opacity: 0.8;
    margin-bottom: 5px;
}

.subscription-info-item .value {
    font-size: 18px;
    font-weight: 600;
}

.pricing-table-container {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

/* Stripe Pricing Tableのレスポンシブ対応 */
stripe-pricing-table {
    width: 100%;
    max-width: 100%;
}

/* ローディングオーバーレイ */
.stripe-loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.stripe-loading-overlay.active {
    display: flex;
}

.stripe-loading-content {
    background: white;
    padding: 30px 40px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
}

.stripe-loading-spinner {
    border: 4px solid #f3f3f3;
    border-top: 4px solid #667eea;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    animation: spin 1s linear infinite;
    margin: 0 auto 15px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* トースト通知 */
.toast-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    min-width: 300px;
    max-width: 500px;
    background: white;
    border-radius: 6px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    padding: 15px 20px;
    z-index: 10000;
    transform: translateX(600px);
    transition: transform 0.3s ease-in-out;
}

.toast-notification.show {
    transform: translateX(0);
}

.toast-notification.error {
    border-left: 4px solid #dc3545;
}

.toast-notification.success {
    border-left: 4px solid #28a745;
}

.toast-notification.warning {
    border-left: 4px solid #ffc107;
}

.toast-notification.info {
    border-left: 4px solid #17a2b8;
}

.toast-notification .toast-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.toast-notification .toast-title {
    font-weight: 600;
    font-size: 14px;
}

.toast-notification .toast-close {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    color: #6c757d;
    padding: 0;
    line-height: 1;
}

.toast-notification .toast-message {
    font-size: 13px;
    color: #495057;
}

/* モバイル対応 */
@media (max-width: 768px) {
    .subscription-info-row {
        flex-direction: column;
    }

    .subscription-info-item {
        margin-bottom: 15px;
    }

    .subscription-info-card {
        padding: 20px;
    }

    .toast-notification {
        right: 10px;
        left: 10px;
        max-width: none;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>料金プラン<small>サブスクリプション管理</small></h1>
    </section>

    <section class="content">
        <!-- 現在の契約情報 -->
        <?php if (isset($subscription_status) && $subscription_status !== 'inactive'): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="subscription-info-card">
                    <h3>
                        現在のご契約
                        <span class="subscription-status <?php echo $subscription_status; ?>">
                            <?php
                            $status_labels = [
                                'active' => '有効',
                                'inactive' => '未契約',
                                'past_due' => '支払期限超過',
                                'canceled' => 'キャンセル済み',
                                'trialing' => '試用期間'
                            ];
                            echo $status_labels[$subscription_status] ?? $subscription_status;
                            ?>
                        </span>
                    </h3>
                    <div class="subscription-info-row">
                        <div class="subscription-info-item">
                            <label>プラン</label>
                            <div class="value"><?php echo htmlspecialchars($subscription_plan ?? '未選択'); ?></div>
                        </div>
                        <?php if (isset($payment_date) && $payment_date): ?>
                        <div class="subscription-info-item">
                            <label>有効期限</label>
                            <div class="value"><?php echo date('Y年m月d日', strtotime($payment_date)); ?></div>
                        </div>
                        <?php endif; ?>
                        <div class="subscription-info-item">
                            <label><a href="<?php echo site_url('company/payment-history'); ?>" style="color: rgba(255,255,255,0.9); text-decoration: underline;">決済履歴を見る &raquo;</a></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- 料金プラン選択 -->
        <div class="row">
            <div class="col-md-12">
                <div class="box box-success">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-credit-card"></i>
                            <?php echo ($subscription_status === 'inactive') ? 'プランを選択' : 'プランを変更'; ?>
                        </h3>
                    </div>
                    <div class="box-body">
                        <div class="pricing-table-container">
                            <script async src="https://js.stripe.com/v3/pricing-table.js"></script>
                            <stripe-pricing-table
                                pricing-table-id="<?php echo htmlspecialchars($stripe_pricing_table_id); ?>"
                                publishable-key="<?php echo htmlspecialchars($stripe_publishable_key); ?>">
                            </stripe-pricing-table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- ローディングオーバーレイ -->
<div id="stripeLoadingOverlay" class="stripe-loading-overlay">
    <div class="stripe-loading-content">
        <div class="stripe-loading-spinner"></div>
        <p style="margin: 0; color: #495057; font-weight: 500;">決済ページへ移動中...</p>
        <p style="margin: 5px 0 0 0; font-size: 12px; color: #6c757d;">しばらくお待ちください</p>
    </div>
</div>

<script>
/**
 * Stripe Pricing Table処理
 */
(function() {
    'use strict';

    /**
     * トースト通知を表示
     */
    function showToast(message, type = 'info', title = '') {
        // 既存のトーストを削除
        const existingToast = document.querySelector('.toast-notification');
        if (existingToast) {
            existingToast.remove();
        }

        // タイトルのデフォルト値
        const titles = {
            'success': '成功',
            'error': 'エラー',
            'warning': '警告',
            'info': '情報'
        };
        const toastTitle = title || titles[type] || '通知';

        // トースト要素を作成
        const toast = document.createElement('div');
        toast.className = 'toast-notification ' + type;
        toast.innerHTML = `
            <div class="toast-header">
                <span class="toast-title">${toastTitle}</span>
                <button class="toast-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
            </div>
            <div class="toast-message">${message}</div>
        `;

        document.body.appendChild(toast);

        // アニメーション表示
        setTimeout(function() {
            toast.classList.add('show');
        }, 100);

        // 5秒後に自動削除
        setTimeout(function() {
            toast.classList.remove('show');
            setTimeout(function() {
                toast.remove();
            }, 300);
        }, 5000);
    }

    /**
     * ローディングオーバーレイを表示/非表示
     */
    function toggleLoading(show) {
        const overlay = document.getElementById('stripeLoadingOverlay');
        if (overlay) {
            if (show) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
    }

    /**
     * Stripe Pricing Tableの監視
     */
    function initStripePricingTable() {
        // Pricing Table要素を取得
        const pricingTable = document.querySelector('stripe-pricing-table');

        if (!pricingTable) {
            console.warn('Stripe Pricing Table not found');
            return;
        }

        // Pricing Tableのクリックイベントを監視
        // 注意: Stripe Pricing Tableは Shadow DOMを使用しているため、
        // 直接のクリックイベント監視は制限されます
        // ここでは、ページ離脱時にローディングを表示します

        // beforeunloadイベントでStripe Checkoutへの遷移を検知
        window.addEventListener('beforeunload', function(e) {
            // Stripeへの遷移の場合、ローディングを表示
            const isStripeRedirect = document.referrer.includes('stripe.com') ||
                                    window.location.href.includes('stripe.com');

            if (isStripeRedirect) {
                toggleLoading(true);
            }
        });

        console.log('Stripe Pricing Table initialized');
    }

    /**
     * エラーハンドリング
     */
    function handlePaymentError(error) {
        console.error('Payment error:', error);

        toggleLoading(false);

        let errorMessage = '決済処理中にエラーが発生しました。';

        if (error && error.message) {
            errorMessage = error.message;
        } else if (typeof error === 'string') {
            errorMessage = error;
        }

        showToast(errorMessage + ' しばらくしてから再度お試しください。', 'error');
    }

    /**
     * ページ読み込み時の初期化
     */
    document.addEventListener('DOMContentLoaded', function() {
        initStripePricingTable();

        // URLパラメータからエラーメッセージを取得
        const urlParams = new URLSearchParams(window.location.search);
        const errorMsg = urlParams.get('error');
        const successMsg = urlParams.get('success');

        if (errorMsg) {
            showToast(decodeURIComponent(errorMsg), 'error');
        }

        if (successMsg) {
            showToast(decodeURIComponent(successMsg), 'success');
        }

        console.log('Payment page initialized');
    });

    // グローバルに公開（必要に応じて）
    window.StripePayment = {
        showToast: showToast,
        toggleLoading: toggleLoading,
        handleError: handlePaymentError
    };

})();
</script>