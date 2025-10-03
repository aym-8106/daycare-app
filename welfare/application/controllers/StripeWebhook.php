<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stripe Webhook Controller
 *
 * Stripeからのイベントを受信・処理するコントローラー
 *
 * エンドポイント: /stripe_webhook/index
 * または: /api/stripe/webhook (routes.phpで設定)
 *
 * @package    DayCare
 * @subpackage Controllers
 * @category   Payment
 * @author     Claude
 * @version    1.0.0
 */
class StripeWebhook extends CI_Controller
{
    /**
     * コンストラクタ
     */
    public function __construct()
    {
        parent::__construct();

        // CSRF保護を明示的に無効化（Webhookエンドポイントのため）
        $this->config->set_item('csrf_protection', FALSE);

        // モデルとライブラリのロード
        $this->load->library('Stripe_lib');
        $this->load->model('Payment_model');
        $this->load->model('Webhook_model');
        $this->load->model('Company_model');

        // Webhookはセキュリティを署名検証で担保
        // Stripe-Signatureヘッダーでリクエストの正当性を検証
    }

    /**
     * Webhook受信エンドポイント
     *
     * Stripeからのイベントを受信・検証・処理します
     *
     * @return void
     */
    public function index()
    {
        // HTTPステータスコード設定用
        $http_status = 200;
        $response = ['received' => true];

        try {
            // 生のPOSTデータを取得
            $payload = file_get_contents('php://input');
            $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

            if (empty($payload)) {
                log_message('error', 'StripeWebhook: 空のペイロード');
                $this->output->set_status_header(400);
                $this->output->set_content_type('application/json');
                echo json_encode(['error' => 'Empty payload']);
                return;
            }

            if (empty($sig_header)) {
                log_message('error', 'StripeWebhook: Stripe-Signatureヘッダーがありません');
                $this->output->set_status_header(400);
                $this->output->set_content_type('application/json');
                echo json_encode(['error' => 'Missing signature']);
                return;
            }

            // Webhook署名を検証してイベント構築
            $event = $this->stripe_lib->constructWebhookEvent($payload, $sig_header);

            log_message('info', 'StripeWebhook: イベント受信 - ' . $event->type . ' (ID: ' . $event->id . ')');

            // 冪等性チェック: 既に処理済みのイベントは無視
            if ($this->webhook_model->isEventProcessed($event->id)) {
                log_message('info', 'StripeWebhook: イベント ' . $event->id . ' は既に処理済みです');
                $this->output->set_content_type('application/json');
                echo json_encode(['received' => true, 'already_processed' => true]);
                return;
            }

            // イベントを記録
            $this->webhook_model->recordEvent($event->id, $event->type, $payload);

            // イベントタイプに応じて処理を振り分け
            $processed = false;

            switch ($event->type) {
                case 'checkout.session.completed':
                    $processed = $this->handleCheckoutSessionCompleted($event);
                    break;

                case 'customer.subscription.created':
                    $processed = $this->handleSubscriptionCreated($event);
                    break;

                case 'customer.subscription.updated':
                    $processed = $this->handleSubscriptionUpdated($event);
                    break;

                case 'customer.subscription.deleted':
                    $processed = $this->handleSubscriptionDeleted($event);
                    break;

                case 'invoice.payment_succeeded':
                    $processed = $this->handleInvoicePaymentSucceeded($event);
                    break;

                case 'invoice.payment_failed':
                    $processed = $this->handleInvoicePaymentFailed($event);
                    break;

                default:
                    // 未対応のイベントタイプはログに記録して無視
                    log_message('info', 'StripeWebhook: 未対応のイベントタイプ - ' . $event->type);
                    $processed = true; // エラーではないので true
                    break;
            }

            // 処理結果を記録
            if ($processed) {
                $this->webhook_model->markAsProcessed($event->id, 'success');
                log_message('info', 'StripeWebhook: イベント ' . $event->id . ' の処理が完了しました');
            } else {
                $this->webhook_model->markAsFailed($event->id, '処理中にエラーが発生しました');
                log_message('error', 'StripeWebhook: イベント ' . $event->id . ' の処理に失敗しました');
                $http_status = 500;
                $response = ['error' => 'Processing failed'];
            }

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: エラー - ' . $e->getMessage());
            $http_status = 400;
            $response = ['error' => $e->getMessage()];

            // イベントIDが取得できていれば失敗としてマーク
            if (isset($event->id)) {
                $this->webhook_model->markAsFailed($event->id, $e->getMessage());
            }
        }

        // レスポンスを返す
        $this->output->set_status_header($http_status);
        $this->output->set_content_type('application/json');
        echo json_encode($response);
    }

    /**
     * checkout.session.completed イベントハンドラ
     *
     * 決済セッション完了時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleCheckoutSessionCompleted($event)
    {
        try {
            $session = $event->data->object;

            // 必須データの取得
            $company_id = $session->metadata->company_id ?? null;
            $customer_id = $session->customer ?? null;
            $subscription_id = $session->subscription ?? null;

            if (empty($company_id)) {
                log_message('error', 'StripeWebhook: company_idがメタデータに含まれていません');
                return false;
            }

            // 事業所情報を更新
            $update_data = [
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'subscription_status' => 'active',
                'subscription_start_date' => date('Y-m-d H:i:s'),
            ];

            // プラン名を取得（メタデータから）
            if (!empty($session->metadata->plan_name)) {
                $update_data['subscription_plan'] = $session->metadata->plan_name;
            }

            $this->payment_model->updateCompanySubscription($company_id, $update_data);

            log_message('info', 'StripeWebhook: 事業所 ' . $company_id . ' のサブスクリプション情報を更新しました');

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: checkout.session.completed 処理エラー - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * customer.subscription.created イベントハンドラ
     *
     * サブスクリプション作成時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleSubscriptionCreated($event)
    {
        try {
            $subscription = $event->data->object;
            $customer_id = $subscription->customer;

            // 顧客IDから事業所を検索
            $company = $this->company_model->read(['stripe_customer_id' => $customer_id]);

            if (empty($company)) {
                log_message('warning', 'StripeWebhook: 顧客ID ' . $customer_id . ' に対応する事業所が見つかりません');
                return true; // エラーではないが処理をスキップ
            }

            $company_id = $company['company_id'];

            // サブスクリプション情報を更新
            $update_data = [
                'stripe_subscription_id' => $subscription->id,
                'subscription_status' => $subscription->status,
                'subscription_start_date' => date('Y-m-d H:i:s', $subscription->current_period_start),
            ];

            $this->payment_model->updateCompanySubscription($company_id, $update_data);

            log_message('info', 'StripeWebhook: サブスクリプション作成 - 事業所 ' . $company_id);

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: subscription.created 処理エラー - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * customer.subscription.updated イベントハンドラ
     *
     * サブスクリプション更新時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleSubscriptionUpdated($event)
    {
        try {
            $subscription = $event->data->object;
            $customer_id = $subscription->customer;

            // 顧客IDから事業所を検索
            $company = $this->company_model->read(['stripe_customer_id' => $customer_id]);

            if (empty($company)) {
                log_message('warning', 'StripeWebhook: 顧客ID ' . $customer_id . ' に対応する事業所が見つかりません');
                return true;
            }

            $company_id = $company['company_id'];

            // サブスクリプション情報を更新
            $update_data = [
                'subscription_status' => $subscription->status,
            ];

            // キャンセル予定の場合
            if ($subscription->cancel_at_period_end) {
                $update_data['subscription_end_date'] = date('Y-m-d H:i:s', $subscription->current_period_end);
            }

            $this->payment_model->updateCompanySubscription($company_id, $update_data);

            log_message('info', 'StripeWebhook: サブスクリプション更新 - 事業所 ' . $company_id . ', ステータス: ' . $subscription->status);

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: subscription.updated 処理エラー - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * customer.subscription.deleted イベントハンドラ
     *
     * サブスクリプションキャンセル時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleSubscriptionDeleted($event)
    {
        try {
            $subscription = $event->data->object;
            $customer_id = $subscription->customer;

            // 顧客IDから事業所を検索
            $company = $this->company_model->read(['stripe_customer_id' => $customer_id]);

            if (empty($company)) {
                log_message('warning', 'StripeWebhook: 顧客ID ' . $customer_id . ' に対応する事業所が見つかりません');
                return true;
            }

            $company_id = $company['company_id'];

            // サブスクリプション情報を更新
            $update_data = [
                'subscription_status' => 'canceled',
                'subscription_end_date' => date('Y-m-d H:i:s'),
            ];

            $this->payment_model->updateCompanySubscription($company_id, $update_data);

            log_message('info', 'StripeWebhook: サブスクリプション削除 - 事業所 ' . $company_id);

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: subscription.deleted 処理エラー - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * invoice.payment_succeeded イベントハンドラ
     *
     * 請求書支払い成功時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleInvoicePaymentSucceeded($event)
    {
        try {
            $invoice = $event->data->object;
            $customer_id = $invoice->customer;
            $subscription_id = $invoice->subscription ?? null;

            // 顧客IDから事業所を検索
            $company = $this->company_model->read(['stripe_customer_id' => $customer_id]);

            if (empty($company)) {
                log_message('warning', 'StripeWebhook: 顧客ID ' . $customer_id . ' に対応する事業所が見つかりません');
                return true;
            }

            $company_id = $company['company_id'];

            // 決済履歴を記録
            $payment_data = [
                'company_id' => $company_id,
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'stripe_invoice_id' => $invoice->id,
                'stripe_payment_intent_id' => $invoice->payment_intent ?? null,
                'amount' => $invoice->amount_paid / 100, // セントから円に変換
                'currency' => $invoice->currency,
                'status' => 'succeeded',
                'plan_name' => $company['subscription_plan'] ?? null,
                'plan_interval' => 'month',
                'payment_date' => date('Y-m-d H:i:s', $invoice->status_transitions->paid_at),
                'next_billing_date' => !empty($invoice->next_payment_attempt) ? date('Y-m-d H:i:s', $invoice->next_payment_attempt) : null,
                'webhook_event_id' => $event->id,
            ];

            $this->payment_model->recordPayment($payment_data);

            // 事業所の payment_date を更新
            $this->payment_model->updateCompanySubscription($company_id, [
                'payment_date' => date('Y-m-d H:i:s'),
                'subscription_status' => 'active',
            ]);

            log_message('info', 'StripeWebhook: 支払い成功 - 事業所 ' . $company_id . ', 金額: ' . $payment_data['amount']);

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: invoice.payment_succeeded 処理エラー - ' . $e->getMessage());
            return false;
        }
    }

    /**
     * invoice.payment_failed イベントハンドラ
     *
     * 請求書支払い失敗時の処理
     *
     * @param object $event Stripeイベント
     * @return bool 処理成功/失敗
     */
    private function handleInvoicePaymentFailed($event)
    {
        try {
            $invoice = $event->data->object;
            $customer_id = $invoice->customer;
            $subscription_id = $invoice->subscription ?? null;

            // 顧客IDから事業所を検索
            $company = $this->company_model->read(['stripe_customer_id' => $customer_id]);

            if (empty($company)) {
                log_message('warning', 'StripeWebhook: 顧客ID ' . $customer_id . ' に対応する事業所が見つかりません');
                return true;
            }

            $company_id = $company['company_id'];

            // 失敗履歴を記録
            $payment_data = [
                'company_id' => $company_id,
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'stripe_invoice_id' => $invoice->id,
                'amount' => $invoice->amount_due / 100,
                'currency' => $invoice->currency,
                'status' => 'failed',
                'plan_name' => $company['subscription_plan'] ?? null,
                'plan_interval' => 'month',
                'payment_date' => date('Y-m-d H:i:s'),
                'failure_reason' => $invoice->last_finalization_error->message ?? '支払い失敗',
                'webhook_event_id' => $event->id,
            ];

            $this->payment_model->recordPayment($payment_data);

            // 事業所のステータスを past_due に更新
            $this->payment_model->updateCompanySubscription($company_id, [
                'subscription_status' => 'past_due',
            ]);

            log_message('warning', 'StripeWebhook: 支払い失敗 - 事業所 ' . $company_id . ', 理由: ' . $payment_data['failure_reason']);

            // TODO: 管理者に通知メール送信

            return true;

        } catch (Exception $e) {
            log_message('error', 'StripeWebhook: invoice.payment_failed 処理エラー - ' . $e->getMessage());
            return false;
        }
    }
}
