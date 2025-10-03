<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Stripe Payment Integration Library
 *
 * Stripe APIを使用した決済処理・サブスクリプション管理を行うライブラリ
 *
 * @package    DayCare
 * @subpackage Libraries
 * @category   Payment
 * @author     Claude
 * @version    1.0.0
 *
 * 使用方法:
 * $this->load->library('stripe_lib');
 * $session = $this->stripe_lib->createCheckoutSession($price_id, $company_id, $email);
 */

// Stripe PHP SDKを読み込み
require_once FCPATH . 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;

class Stripe_lib
{
    /**
     * CodeIgniterインスタンス
     * @var object
     */
    protected $CI;

    /**
     * Stripe APIシークレットキー
     * @var string
     */
    protected $secret_key;

    /**
     * Stripe API公開キー
     * @var string
     */
    protected $publishable_key;

    /**
     * Stripe Webhookシークレット
     * @var string
     */
    protected $webhook_secret;

    /**
     * Stripe API環境 (test or live)
     * @var string
     */
    protected $environment;

    /**
     * Stripeクライアント
     * @var StripeClient
     */
    protected $stripe;

    /**
     * ログ有効フラグ
     * @var bool
     */
    protected $log_enabled;

    /**
     * コンストラクタ
     *
     * Stripe設定を読み込み、Stripe APIを初期化
     *
     * @throws Exception Stripe設定ファイルが読み込めない場合
     */
    public function __construct()
    {
        // CodeIgniterインスタンスを取得
        $this->CI =& get_instance();

        // 設定ファイルを読み込み
        $this->CI->config->load('stripe_config', TRUE);

        // Stripe設定を取得
        $stripe_config = $this->CI->config->item('stripe_config');

        if (!$stripe_config) {
            log_message('error', 'Stripe_lib: stripe_config.php が読み込めませんでした');
            throw new Exception('Stripe configuration not found');
        }

        // 設定値を取得
        $this->environment = $stripe_config['stripe_environment'] ?? 'test';
        $this->secret_key = $stripe_config['stripe_secret_key'] ?? '';
        $this->publishable_key = $stripe_config['stripe_publishable_key'] ?? '';
        $this->webhook_secret = $stripe_config['stripe_webhook_secret'] ?? '';
        $this->log_enabled = $stripe_config['stripe_log_enabled'] ?? FALSE;

        // APIキーが設定されているか確認
        if (empty($this->secret_key)) {
            $error_msg = 'Stripe_lib: シークレットキーが設定されていません (環境: ' . $this->environment . ')';
            log_message('error', $error_msg);
            throw new Exception('Stripe secret key not configured');
        }

        // Stripe APIを初期化
        try {
            Stripe::setApiKey($this->secret_key);

            // API バージョンを設定
            if (!empty($stripe_config['stripe_api_version'])) {
                Stripe::setApiVersion($stripe_config['stripe_api_version']);
            }

            // Stripeクライアントを初期化
            $this->stripe = new StripeClient($this->secret_key);

            $this->_log('info', 'Stripe_lib initialized successfully (environment: ' . $this->environment . ')');

        } catch (Exception $e) {
            log_message('error', 'Stripe_lib: 初期化エラー - ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Stripe APIシークレットキーを取得
     *
     * @return string
     */
    public function getSecretKey()
    {
        return $this->secret_key;
    }

    /**
     * Stripe API公開キーを取得
     *
     * @return string
     */
    public function getPublishableKey()
    {
        return $this->publishable_key;
    }

    /**
     * Stripe Webhookシークレットを取得
     *
     * @return string
     */
    public function getWebhookSecret()
    {
        return $this->webhook_secret;
    }

    /**
     * 現在の環境を取得 (test or live)
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Stripeクライアントを取得
     *
     * @return StripeClient
     */
    public function getStripeClient()
    {
        return $this->stripe;
    }

    /**
     * テストモードかどうかを判定
     *
     * @return bool
     */
    public function isTestMode()
    {
        return $this->environment === 'test';
    }

    /**
     * Stripe Checkoutセッションを作成
     *
     * サブスクリプション登録用のCheckoutセッションを作成します
     *
     * @param string $price_id Stripe Price ID (price_xxxxx)
     * @param int $company_id 事業所ID
     * @param string $company_email 事業所メールアドレス
     * @param array $metadata 追加のメタデータ（任意）
     * @return array セッション情報 ['session_id' => 'cs_xxxxx', 'url' => 'https://...']
     * @throws Exception Stripe APIエラー時
     */
    public function createCheckoutSession($price_id, $company_id, $company_email, $metadata = [])
    {
        try {
            $this->_log('info', 'Creating checkout session', [
                'price_id' => $price_id,
                'company_id' => $company_id
            ]);

            // 既存の顧客情報を取得（Company_modelを使用）
            $this->CI->load->model('Company_model');
            $company = $this->CI->Company_model->read(['company_id' => $company_id]);

            // リダイレクトURLを構築（base_url()を使用）
            $this->CI->load->helper('url');
            $stripe_config = $this->CI->config->item('stripe_config');
            $success_url = base_url($stripe_config['stripe_success_url_path']) . '?session_id={CHECKOUT_SESSION_ID}';
            $cancel_url = base_url($stripe_config['stripe_cancel_url_path']);

            // セッションパラメータを構築
            $session_params = [
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $price_id,
                    'quantity' => 1,
                ]],
                'success_url' => $success_url,
                'cancel_url' => $cancel_url,
                'metadata' => array_merge([
                    'company_id' => $company_id,
                    'source' => 'daycare_app'
                ], $metadata),
            ];

            // 既存の顧客IDがある場合は使用、なければメールアドレスで新規作成
            if (!empty($company['stripe_customer_id'])) {
                $session_params['customer'] = $company['stripe_customer_id'];
                $this->_log('info', 'Using existing Stripe customer', [
                    'customer_id' => $company['stripe_customer_id']
                ]);
            } else {
                $session_params['customer_email'] = $company_email;
                $this->_log('info', 'Creating new Stripe customer with email', [
                    'email' => $company_email
                ]);
            }

            // Stripe Checkout Session作成
            $session = \Stripe\Checkout\Session::create($session_params);

            $this->_log('info', 'Checkout session created successfully', [
                'session_id' => $session->id,
                'url' => $session->url
            ]);

            return [
                'session_id' => $session->id,
                'url' => $session->url,
            ];

        } catch (ApiErrorException $e) {
            $this->_log('error', 'Stripe API error: ' . $e->getMessage(), [
                'error_code' => $e->getError()->code,
                'error_type' => $e->getError()->type
            ]);
            throw new Exception('決済セッションの作成に失敗しました: ' . $e->getMessage());

        } catch (Exception $e) {
            $this->_log('error', 'Error creating checkout session: ' . $e->getMessage());
            throw new Exception('決済セッションの作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Webhook イベントを構築・検証
     *
     * Stripeから送信されたWebhookペイロードを検証し、イベントオブジェクトを構築します
     *
     * @param string $payload WebhookリクエストボディのJSON文字列
     * @param string $signature Stripe-Signature ヘッダーの値
     * @return \Stripe\Event 検証済みのStripeイベント
     * @throws Exception 署名検証失敗時
     */
    public function constructWebhookEvent($payload, $signature)
    {
        try {
            $this->_log('info', 'Constructing webhook event');

            // Webhook署名を検証してイベント構築
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $this->webhook_secret
            );

            $this->_log('info', 'Webhook event verified successfully', [
                'event_id' => $event->id,
                'event_type' => $event->type
            ]);

            return $event;

        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            $this->_log('error', 'Webhook signature verification failed: ' . $e->getMessage());
            throw new Exception('Webhook署名検証に失敗しました');

        } catch (Exception $e) {
            $this->_log('error', 'Error constructing webhook event: ' . $e->getMessage());
            throw new Exception('Webhookイベント構築に失敗しました');
        }
    }

    /**
     * サブスクリプション情報を取得
     *
     * @param string $subscription_id StripeサブスクリプションID (sub_xxxxx)
     * @return \Stripe\Subscription サブスクリプション情報
     * @throws Exception Stripe APIエラー時
     */
    public function getSubscription($subscription_id)
    {
        try {
            $this->_log('info', 'Retrieving subscription', [
                'subscription_id' => $subscription_id
            ]);

            $subscription = $this->stripe->subscriptions->retrieve($subscription_id);

            $this->_log('info', 'Subscription retrieved successfully', [
                'status' => $subscription->status,
                'current_period_end' => $subscription->current_period_end
            ]);

            return $subscription;

        } catch (ApiErrorException $e) {
            $this->_log('error', 'Stripe API error: ' . $e->getMessage());
            throw new Exception('サブスクリプション情報の取得に失敗しました');
        }
    }

    /**
     * サブスクリプションをキャンセル
     *
     * @param string $subscription_id StripeサブスクリプションID (sub_xxxxx)
     * @param bool $at_period_end 期間終了時にキャンセル（デフォルト: true）
     * @return \Stripe\Subscription キャンセル後のサブスクリプション情報
     * @throws Exception Stripe APIエラー時
     */
    public function cancelSubscription($subscription_id, $at_period_end = true)
    {
        try {
            $this->_log('info', 'Canceling subscription', [
                'subscription_id' => $subscription_id,
                'at_period_end' => $at_period_end
            ]);

            if ($at_period_end) {
                // 期間終了時にキャンセル
                $subscription = $this->stripe->subscriptions->update($subscription_id, [
                    'cancel_at_period_end' => true
                ]);
            } else {
                // 即座にキャンセル
                $subscription = $this->stripe->subscriptions->cancel($subscription_id);
            }

            $this->_log('info', 'Subscription canceled successfully', [
                'status' => $subscription->status,
                'cancel_at_period_end' => $subscription->cancel_at_period_end
            ]);

            return $subscription;

        } catch (ApiErrorException $e) {
            $this->_log('error', 'Stripe API error: ' . $e->getMessage());
            throw new Exception('サブスクリプションのキャンセルに失敗しました');
        }
    }

    /**
     * 顧客情報を取得
     *
     * @param string $customer_id Stripe顧客ID (cus_xxxxx)
     * @return \Stripe\Customer 顧客情報
     * @throws Exception Stripe APIエラー時
     */
    public function getCustomer($customer_id)
    {
        try {
            $this->_log('info', 'Retrieving customer', [
                'customer_id' => $customer_id
            ]);

            $customer = $this->stripe->customers->retrieve($customer_id);

            $this->_log('info', 'Customer retrieved successfully', [
                'email' => $customer->email
            ]);

            return $customer;

        } catch (ApiErrorException $e) {
            $this->_log('error', 'Stripe API error: ' . $e->getMessage());
            throw new Exception('顧客情報の取得に失敗しました');
        }
    }

    /**
     * 請求書情報を取得
     *
     * @param string $invoice_id Stripe請求書ID (in_xxxxx)
     * @return \Stripe\Invoice 請求書情報
     * @throws Exception Stripe APIエラー時
     */
    public function getInvoice($invoice_id)
    {
        try {
            $this->_log('info', 'Retrieving invoice', [
                'invoice_id' => $invoice_id
            ]);

            $invoice = $this->stripe->invoices->retrieve($invoice_id);

            $this->_log('info', 'Invoice retrieved successfully', [
                'amount_paid' => $invoice->amount_paid,
                'status' => $invoice->status
            ]);

            return $invoice;

        } catch (ApiErrorException $e) {
            $this->_log('error', 'Stripe API error: ' . $e->getMessage());
            throw new Exception('請求書情報の取得に失敗しました');
        }
    }

    /**
     * ログ出力
     *
     * @param string $level ログレベル (debug, info, warning, error)
     * @param string $message ログメッセージ
     * @param array $context 追加コンテキスト
     */
    protected function _log($level, $message, $context = [])
    {
        if (!$this->log_enabled) {
            return;
        }

        $log_message = 'Stripe_lib: ' . $message;

        if (!empty($context)) {
            $log_message .= ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        log_message($level, $log_message);
    }
}
