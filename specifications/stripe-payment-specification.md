# Stripe決済機能 詳細仕様書

**プロジェクト**: DayCare.app - 訪問看護・福祉事業所管理システム
**作成日**: 2025-10-01
**バージョン**: 1.0
**関連要件書**: [requirements/stripe-payment-requirements.md](../requirements/stripe-payment-requirements.md)
**対象システム**: CodeIgniter 3.x + Stripe PHP SDK v17.2

---

## 目次

1. [文書情報](#1-文書情報)
2. [システム概要](#2-システム概要)
3. [データベース設計](#3-データベース設計)
4. [ファイル・クラス設計](#4-ファイルクラス設計)
5. [API仕様](#5-api仕様)
6. [Webhook処理仕様](#6-webhook処理仕様)
7. [UI/UX設計](#7-uiux設計)
8. [セキュリティ実装](#8-セキュリティ実装)
9. [テスト仕様](#9-テスト仕様)
10. [デプロイ手順](#10-デプロイ手順)
11. [運用・保守](#11-運用保守)

---

## 1. 文書情報

### 1.1 目的
本仕様書は、Stripe決済機能の実装に必要な技術的詳細を定義し、開発者が迷わず実装できるレベルの情報を提供します。

### 1.2 対象読者
- バックエンドエンジニア（PHP/CodeIgniter経験者）
- フロントエンドエンジニア
- QAエンジニア
- DevOpsエンジニア

### 1.3 前提知識
- CodeIgniter 3.x フレームワーク
- Stripe API の基本概念
- MySQLデータベース
- RESTful API

---

## 2. システム概要

### 2.1 アーキテクチャ図

```
┌─────────────┐
│   Browser   │
└──────┬──────┘
       │ HTTPS
       ▼
┌──────────────────────────────────────┐
│   CodeIgniter Application (welfare)  │
│  ┌─────────────────────────────────┐ │
│  │  Controllers                    │ │
│  │  - Company.php                  │ │
│  │  - StripeWebhook.php            │ │
│  └─────────┬───────────────────────┘ │
│            │                          │
│  ┌─────────▼───────────────────────┐ │
│  │  Libraries                      │ │
│  │  - Stripe_lib.php               │ │
│  └─────────┬───────────────────────┘ │
│            │                          │
│  ┌─────────▼───────────────────────┐ │
│  │  Models                         │ │
│  │  - Payment_model.php            │ │
│  │  - Stripe_model.php             │ │
│  │  - Company_model.php            │ │
│  └─────────┬───────────────────────┘ │
└────────────┼──────────────────────────┘
             │
             ▼
      ┌────────────┐       ┌──────────────┐
      │   MySQL    │◄──────┤ Stripe API   │
      │  Database  │       │ (External)   │
      └────────────┘       └──────────────┘
                                  ▲
                                  │ Webhooks
                                  │
                    /api/stripe/webhook
```

### 2.2 技術スタック

| レイヤー | 技術 | バージョン |
|---------|------|-----------|
| フロントエンド | Bootstrap 3.x, jQuery | 3.x, 3.x |
| バックエンド | PHP, CodeIgniter | 7.4+, 3.x |
| 決済 | Stripe PHP SDK | 17.2+ |
| データベース | MySQL/MariaDB | 5.7+/10.2+ |
| Webサーバー | Apache (XAMPP) | 2.4+ |

### 2.3 データフロー

#### 2.3.1 新規決済フロー
```
User → [料金プラン選択] → Company Controller
         ↓
[Checkoutセッション作成] → Stripe API
         ↓
User redirected to Stripe Checkout
         ↓
User enters card info on Stripe
         ↓
Payment processed by Stripe
         ↓
Webhook sent to /api/stripe/webhook
         ↓
StripeWebhook Controller → Payment_model
         ↓
Update tbl_company.payment_date
         ↓
Record in tbl_payment_history
         ↓
User redirected back to success page
```

---

## 3. データベース設計

### 3.1 新規テーブル定義

#### 3.1.1 tbl_payment_history（決済履歴）

```sql
CREATE TABLE `tbl_payment_history` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '決済履歴ID',
  `company_id` INT(11) NOT NULL COMMENT '事業所ID (tbl_companyへの外部キー)',
  `stripe_customer_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe顧客ID (cus_xxxxx)',
  `stripe_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeサブスクリプションID (sub_xxxxx)',
  `stripe_invoice_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe請求書ID (in_xxxxx)',
  `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe PaymentIntent ID (pi_xxxxx)',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '決済金額',
  `currency` VARCHAR(3) DEFAULT 'jpy' COMMENT '通貨コード (ISO 4217)',
  `status` VARCHAR(50) NOT NULL COMMENT '決済ステータス (succeeded, failed, pending, refunded)',
  `plan_name` VARCHAR(255) DEFAULT NULL COMMENT 'プラン名 (例: スタンダードプラン)',
  `plan_interval` VARCHAR(20) DEFAULT NULL COMMENT '請求間隔 (month, year)',
  `payment_date` DATETIME NOT NULL COMMENT '決済日時',
  `next_billing_date` DATETIME DEFAULT NULL COMMENT '次回請求日',
  `webhook_event_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeイベントID (evt_xxxxx)',
  `failure_reason` TEXT DEFAULT NULL COMMENT '失敗理由',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',

  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_stripe_customer` (`stripe_customer_id`),
  INDEX `idx_stripe_subscription` (`stripe_subscription_id`),
  INDEX `idx_payment_date` (`payment_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='決済履歴テーブル';
```

#### 3.1.2 tbl_stripe_webhooks（Webhook管理）

```sql
CREATE TABLE `tbl_stripe_webhooks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Webhook記録ID',
  `event_id` VARCHAR(255) UNIQUE NOT NULL COMMENT 'StripeイベントID (evt_xxxxx)',
  `event_type` VARCHAR(100) NOT NULL COMMENT 'イベントタイプ (例: checkout.session.completed)',
  `processed` TINYINT(1) DEFAULT 0 COMMENT '処理済みフラグ (0: 未処理, 1: 処理済み)',
  `payload` LONGTEXT DEFAULT NULL COMMENT 'イベントペイロード (JSON形式)',
  `processing_result` TEXT DEFAULT NULL COMMENT '処理結果 (成功/失敗理由)',
  `received_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '受信日時',
  `processed_at` DATETIME DEFAULT NULL COMMENT '処理完了日時',

  INDEX `idx_event_id` (`event_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_processed` (`processed`),
  INDEX `idx_received_at` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Webhook受信管理テーブル（冪等性保証用）';
```

### 3.2 既存テーブルの変更

#### 3.2.1 tbl_company（事業所マスタ）

```sql
-- Stripe関連カラムの追加
ALTER TABLE `tbl_company`
ADD COLUMN `stripe_customer_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe顧客ID' AFTER `payment_date`,
ADD COLUMN `stripe_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT '現在のサブスクリプションID' AFTER `stripe_customer_id`,
ADD COLUMN `subscription_status` VARCHAR(50) DEFAULT 'inactive' COMMENT 'サブスクリプションステータス (active, inactive, past_due, canceled, trialing)' AFTER `stripe_subscription_id`,
ADD COLUMN `subscription_plan` VARCHAR(100) DEFAULT NULL COMMENT 'プラン名' AFTER `subscription_status`,
ADD COLUMN `subscription_start_date` DATETIME DEFAULT NULL COMMENT 'サブスクリプション開始日' AFTER `subscription_plan`,
ADD COLUMN `subscription_end_date` DATETIME DEFAULT NULL COMMENT 'サブスクリプション終了日（キャンセル時）' AFTER `subscription_start_date`,
ADD INDEX `idx_stripe_customer` (`stripe_customer_id`),
ADD INDEX `idx_subscription_status` (`subscription_status`);
```

### 3.3 ER図

```
┌─────────────────────────────────────┐
│         tbl_company                 │
├─────────────────────────────────────┤
│ * id (PK)                           │
│   company_name                      │
│   email                             │
│   payment_date                      │
│   stripe_customer_id                │
│   stripe_subscription_id            │
│   subscription_status               │
│   subscription_plan                 │
│   ...                               │
└──────────┬──────────────────────────┘
           │ 1:N
           │
           ▼
┌─────────────────────────────────────┐
│    tbl_payment_history              │
├─────────────────────────────────────┤
│ * id (PK)                           │
│   company_id (FK)                   │
│   stripe_customer_id                │
│   stripe_subscription_id            │
│   amount                            │
│   status                            │
│   payment_date                      │
│   ...                               │
└─────────────────────────────────────┘

┌─────────────────────────────────────┐
│    tbl_stripe_webhooks              │
├─────────────────────────────────────┤
│ * id (PK)                           │
│   event_id (UNIQUE)                 │
│   event_type                        │
│   processed                         │
│   payload                           │
│   ...                               │
└─────────────────────────────────────┘
```

### 3.4 インデックス設計

| テーブル | カラム | インデックス名 | 目的 |
|---------|--------|--------------|------|
| tbl_payment_history | company_id | idx_company_id | 事業所別の履歴取得高速化 |
| tbl_payment_history | stripe_customer_id | idx_stripe_customer | 顧客IDでの検索 |
| tbl_payment_history | payment_date | idx_payment_date | 日付範囲検索 |
| tbl_stripe_webhooks | event_id | idx_event_id | 冪等性チェック高速化 |
| tbl_stripe_webhooks | processed | idx_processed | 未処理イベント抽出 |
| tbl_company | stripe_customer_id | idx_stripe_customer | 顧客IDでの検索 |

---

## 4. ファイル・クラス設計

### 4.1 ディレクトリ構成

```
DayCare.app/welfare/
├── application/
│   ├── config/
│   │   ├── stripe_config.php          [新規] Stripe設定
│   │   ├── config.php                 [変更] ルートURL設定
│   │   └── routes.php                 [変更] Webhookルート追加
│   ├── controllers/
│   │   ├── Company.php                [変更] 決済関連メソッド追加
│   │   └── StripeWebhook.php          [新規] Webhook処理
│   ├── libraries/
│   │   └── Stripe_lib.php             [新規] Stripe操作ライブラリ
│   ├── models/
│   │   ├── Company_model.php          [変更] Stripe関連メソッド追加
│   │   ├── Payment_model.php          [新規] 決済履歴モデル
│   │   └── Webhook_model.php          [新規] Webhook管理モデル
│   ├── views/
│   │   └── company/
│   │       ├── payment.php            [変更] Pricing Table統合
│   │       ├── payment_success.php    [新規] 決済成功ページ
│   │       ├── payment_cancel.php     [新規] 決済キャンセルページ
│   │       └── payment_history.php    [新規] 決済履歴ページ
│   └── logs/
│       └── stripe_YYYY-MM-DD.log      [自動生成] Stripeログ
└── vendor/
    └── stripe/                         [既存] Stripe PHP SDK
```

### 4.2 設定ファイル

#### 4.2.1 application/config/stripe_config.php [新規]

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Stripe Configuration
|--------------------------------------------------------------------------
|
| Stripe API キーと設定
| 本番環境では環境変数または暗号化された設定ファイルから読み込むこと
|
*/

// 環境の切り替え (test / live)
$config['stripe_environment'] = 'test'; // 本番では 'live' に変更

// テストモードのAPIキー
$config['stripe_test_publishable_key'] = 'pk_test_xxxxxxxxxxxxxxxxxxxxx';
$config['stripe_test_secret_key'] = 'sk_test_xxxxxxxxxxxxxxxxxxxxx';
$config['stripe_test_webhook_secret'] = 'whsec_xxxxxxxxxxxxxxxxxxxxx';

// 本番モードのAPIキー（環境変数から取得推奨）
$config['stripe_live_publishable_key'] = getenv('STRIPE_LIVE_PUBLISHABLE_KEY') ?: '';
$config['stripe_live_secret_key'] = getenv('STRIPE_LIVE_SECRET_KEY') ?: '';
$config['stripe_live_webhook_secret'] = getenv('STRIPE_LIVE_WEBHOOK_SECRET') ?: '';

// 現在の環境に応じたキーを設定
if ($config['stripe_environment'] === 'live') {
    $config['stripe_publishable_key'] = $config['stripe_live_publishable_key'];
    $config['stripe_secret_key'] = $config['stripe_live_secret_key'];
    $config['stripe_webhook_secret'] = $config['stripe_live_webhook_secret'];
} else {
    $config['stripe_publishable_key'] = $config['stripe_test_publishable_key'];
    $config['stripe_secret_key'] = $config['stripe_test_secret_key'];
    $config['stripe_webhook_secret'] = $config['stripe_test_webhook_secret'];
}

// Stripe APIバージョン
$config['stripe_api_version'] = '2023-10-16';

// 通貨
$config['stripe_currency'] = 'jpy';

// 成功・キャンセル時のリダイレクトURL
$config['stripe_success_url'] = base_url('company/payment-success');
$config['stripe_cancel_url'] = base_url('company/payment-cancel');

// Pricing Table ID（Stripe Dashboardで作成）
$config['stripe_pricing_table_id'] = 'prctbl_xxxxxxxxxxxxxxxxxxxxx';

// ログ設定
$config['stripe_log_enabled'] = TRUE;
$config['stripe_log_path'] = APPPATH . 'logs/stripe_';
```

### 4.3 ライブラリ実装

#### 4.3.1 application/libraries/Stripe_lib.php [新規]

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once FCPATH . 'vendor/autoload.php';

use Stripe\Stripe;
use Stripe\Checkout\Session;
use Stripe\Customer;
use Stripe\Subscription;
use Stripe\BillingPortal\Session as PortalSession;

/**
 * Stripe操作ライブラリ
 *
 * Stripe APIとのやり取りを管理するライブラリ
 */
class Stripe_lib
{
    protected $CI;
    protected $stripe_config;
    protected $secret_key;
    protected $publishable_key;

    /**
     * コンストラクタ
     */
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->config('stripe_config');

        // 設定を取得
        $this->stripe_config = $this->CI->config->item('stripe_environment');
        $this->secret_key = $this->CI->config->item('stripe_secret_key');
        $this->publishable_key = $this->CI->config->item('stripe_publishable_key');

        // Stripe APIキーを設定
        Stripe::setApiKey($this->secret_key);

        // APIバージョンを設定
        $api_version = $this->CI->config->item('stripe_api_version');
        if ($api_version) {
            Stripe::setApiVersion($api_version);
        }

        $this->log('Stripe library initialized');
    }

    /**
     * Checkoutセッションを作成
     *
     * @param string $price_id Stripe Price ID
     * @param int $company_id 事業所ID
     * @param string $company_email 事業所メールアドレス
     * @param array $metadata 追加のメタデータ
     * @return array セッション情報 ['session_id' => '', 'url' => '']
     * @throws Exception Stripe APIエラー時
     */
    public function createCheckoutSession($price_id, $company_id, $company_email, $metadata = [])
    {
        try {
            // 既存の顧客IDを取得
            $this->CI->load->model('Company_model');
            $company = $this->CI->Company_model->get_by_id($company_id);

            $session_params = [
                'mode' => 'subscription',
                'line_items' => [[
                    'price' => $price_id,
                    'quantity' => 1,
                ]],
                'success_url' => $this->CI->config->item('stripe_success_url') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $this->CI->config->item('stripe_cancel_url'),
                'metadata' => array_merge([
                    'company_id' => $company_id,
                ], $metadata),
            ];

            // 既存の顧客IDがある場合は使用
            if (!empty($company['stripe_customer_id'])) {
                $session_params['customer'] = $company['stripe_customer_id'];
            } else {
                // 新規顧客の場合はメールアドレスを設定
                $session_params['customer_email'] = $company_email;
            }

            // Checkoutセッション作成
            $session = Session::create($session_params);

            $this->log('Checkout session created', [
                'session_id' => $session->id,
                'company_id' => $company_id
            ]);

            return [
                'session_id' => $session->id,
                'url' => $session->url,
            ];

        } catch (\Exception $e) {
            $this->log('Error creating checkout session: ' . $e->getMessage(), [], 'error');
            throw new Exception('決済セッションの作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * Stripe顧客を作成
     *
     * @param string $email メールアドレス
     * @param string $name 顧客名
     * @param array $metadata メタデータ
     * @return string 顧客ID (cus_xxxxx)
     * @throws Exception
     */
    public function createCustomer($email, $name, $metadata = [])
    {
        try {
            $customer = Customer::create([
                'email' => $email,
                'name' => $name,
                'metadata' => $metadata,
            ]);

            $this->log('Customer created', ['customer_id' => $customer->id]);

            return $customer->id;

        } catch (\Exception $e) {
            $this->log('Error creating customer: ' . $e->getMessage(), [], 'error');
            throw new Exception('顧客の作成に失敗しました: ' . $e->getMessage());
        }
    }

    /**
     * サブスクリプション情報を取得
     *
     * @param string $subscription_id サブスクリプションID
     * @return array サブスクリプション情報
     * @throws Exception
     */
    public function retrieveSubscription($subscription_id)
    {
        try {
            $subscription = Subscription::retrieve($subscription_id);

            return [
                'id' => $subscription->id,
                'status' => $subscription->status,
                'current_period_end' => $subscription->current_period_end,
                'current_period_start' => $subscription->current_period_start,
                'plan' => $subscription->items->data[0]->price->id ?? null,
                'plan_amount' => $subscription->items->data[0]->price->unit_amount ?? 0,
                'plan_interval' => $subscription->items->data[0]->price->recurring->interval ?? null,
            ];

        } catch (\Exception $e) {
            $this->log('Error retrieving subscription: ' . $e->getMessage(), [], 'error');
            throw new Exception('サブスクリプション情報の取得に失敗しました');
        }
    }

    /**
     * カスタマーポータルセッションを作成
     *
     * @param string $customer_id Stripe顧客ID
     * @return string ポータルURL
     * @throws Exception
     */
    public function createPortalSession($customer_id)
    {
        try {
            $session = PortalSession::create([
                'customer' => $customer_id,
                'return_url' => base_url('company/payment'),
            ]);

            $this->log('Portal session created', ['customer_id' => $customer_id]);

            return $session->url;

        } catch (\Exception $e) {
            $this->log('Error creating portal session: ' . $e->getMessage(), [], 'error');
            throw new Exception('ポータルセッションの作成に失敗しました');
        }
    }

    /**
     * Webhook署名を検証
     *
     * @param string $payload リクエストボディ（生のJSON）
     * @param string $signature Stripe-Signature ヘッダー
     * @return object Stripeイベントオブジェクト
     * @throws Exception 署名検証失敗時
     */
    public function constructWebhookEvent($payload, $signature)
    {
        try {
            $webhook_secret = $this->CI->config->item('stripe_webhook_secret');

            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $signature,
                $webhook_secret
            );

            return $event;

        } catch (\UnexpectedValueException $e) {
            $this->log('Invalid webhook payload: ' . $e->getMessage(), [], 'error');
            throw new Exception('無効なペイロード');
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            $this->log('Invalid webhook signature: ' . $e->getMessage(), [], 'error');
            throw new Exception('署名検証失敗');
        }
    }

    /**
     * ログを記録
     *
     * @param string $message ログメッセージ
     * @param array $context コンテキスト情報
     * @param string $level ログレベル (info, error, debug)
     */
    protected function log($message, $context = [], $level = 'info')
    {
        if (!$this->CI->config->item('stripe_log_enabled')) {
            return;
        }

        $log_path = $this->CI->config->item('stripe_log_path') . date('Y-m-d') . '.log';
        $log_message = date('Y-m-d H:i:s') . " [{$level}] {$message}";

        if (!empty($context)) {
            $log_message .= ' | Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
        }

        $log_message .= PHP_EOL;

        file_put_contents($log_path, $log_message, FILE_APPEND);
    }
}
```

### 4.4 モデル実装

#### 4.4.1 application/models/Payment_model.php [新規]

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 決済履歴モデル
 */
class Payment_model extends CI_Model
{
    protected $table = 'tbl_payment_history';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * 決済履歴を記録
     *
     * @param array $data 決済データ
     * @return int 挿入されたID
     */
    public function recordPayment($data)
    {
        $record = [
            'company_id' => $data['company_id'],
            'stripe_customer_id' => $data['stripe_customer_id'] ?? null,
            'stripe_subscription_id' => $data['stripe_subscription_id'] ?? null,
            'stripe_invoice_id' => $data['stripe_invoice_id'] ?? null,
            'stripe_payment_intent_id' => $data['stripe_payment_intent_id'] ?? null,
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'jpy',
            'status' => $data['status'],
            'plan_name' => $data['plan_name'] ?? null,
            'plan_interval' => $data['plan_interval'] ?? null,
            'payment_date' => date('Y-m-d H:i:s'),
            'next_billing_date' => $data['next_billing_date'] ?? null,
            'webhook_event_id' => $data['webhook_event_id'] ?? null,
            'failure_reason' => $data['failure_reason'] ?? null,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $record);
        return $this->db->insert_id();
    }

    /**
     * 決済履歴を取得
     *
     * @param int $company_id 事業所ID
     * @param int $limit 取得件数
     * @param int $offset オフセット
     * @return array 決済履歴の配列
     */
    public function getPaymentHistory($company_id, $limit = 10, $offset = 0)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->order_by('payment_date', 'DESC');
        $this->db->limit($limit, $offset);

        $query = $this->db->get();
        return $query->result_array();
    }

    /**
     * 決済履歴の総件数を取得
     *
     * @param int $company_id 事業所ID
     * @return int 総件数
     */
    public function getPaymentHistoryCount($company_id)
    {
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        return $this->db->count_all_results();
    }

    /**
     * 最新の決済履歴を取得
     *
     * @param int $company_id 事業所ID
     * @return array|null 決済履歴
     */
    public function getLatestPayment($company_id)
    {
        $this->db->select('*');
        $this->db->from($this->table);
        $this->db->where('company_id', $company_id);
        $this->db->where('status', 'succeeded');
        $this->db->order_by('payment_date', 'DESC');
        $this->db->limit(1);

        $query = $this->db->get();
        return $query->row_array();
    }

    /**
     * 事業所のサブスクリプション情報を更新
     *
     * @param int $company_id 事業所ID
     * @param array $subscription_data サブスクリプションデータ
     * @return bool 成功/失敗
     */
    public function updateCompanySubscription($company_id, $subscription_data)
    {
        $update_data = [];

        if (isset($subscription_data['stripe_customer_id'])) {
            $update_data['stripe_customer_id'] = $subscription_data['stripe_customer_id'];
        }

        if (isset($subscription_data['stripe_subscription_id'])) {
            $update_data['stripe_subscription_id'] = $subscription_data['stripe_subscription_id'];
        }

        if (isset($subscription_data['subscription_status'])) {
            $update_data['subscription_status'] = $subscription_data['subscription_status'];
        }

        if (isset($subscription_data['subscription_plan'])) {
            $update_data['subscription_plan'] = $subscription_data['subscription_plan'];
        }

        if (isset($subscription_data['payment_date'])) {
            $update_data['payment_date'] = $subscription_data['payment_date'];
        }

        if (isset($subscription_data['subscription_start_date'])) {
            $update_data['subscription_start_date'] = $subscription_data['subscription_start_date'];
        }

        if (isset($subscription_data['subscription_end_date'])) {
            $update_data['subscription_end_date'] = $subscription_data['subscription_end_date'];
        }

        if (empty($update_data)) {
            return false;
        }

        $this->db->where('id', $company_id);
        return $this->db->update('tbl_company', $update_data);
    }
}
```

#### 4.4.2 application/models/Webhook_model.php [新規]

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Webhook管理モデル
 * 冪等性を保証するためのイベント管理
 */
class Webhook_model extends CI_Model
{
    protected $table = 'tbl_stripe_webhooks';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    /**
     * イベントが既に処理済みかチェック
     *
     * @param string $event_id StripeイベントID
     * @return bool true=処理済み, false=未処理
     */
    public function isEventProcessed($event_id)
    {
        $this->db->where('event_id', $event_id);
        $this->db->where('processed', 1);
        $query = $this->db->get($this->table);

        return $query->num_rows() > 0;
    }

    /**
     * イベントを記録
     *
     * @param string $event_id イベントID
     * @param string $event_type イベントタイプ
     * @param string $payload ペイロード（JSON）
     * @return int 挿入されたID
     */
    public function recordEvent($event_id, $event_type, $payload)
    {
        // 既に存在する場合は何もしない
        $this->db->where('event_id', $event_id);
        $existing = $this->db->get($this->table);

        if ($existing->num_rows() > 0) {
            return $existing->row()->id;
        }

        $data = [
            'event_id' => $event_id,
            'event_type' => $event_type,
            'processed' => 0,
            'payload' => $payload,
            'received_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    /**
     * イベントを処理済みとしてマーク
     *
     * @param string $event_id イベントID
     * @param string $processing_result 処理結果
     * @return bool 成功/失敗
     */
    public function markAsProcessed($event_id, $processing_result = 'success')
    {
        $data = [
            'processed' => 1,
            'processing_result' => $processing_result,
            'processed_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->where('event_id', $event_id);
        return $this->db->update($this->table, $data);
    }
}
```

---

## 5. API仕様

### 5.1 エンドポイント一覧

| メソッド | エンドポイント | 説明 | 認証 | 実装ファイル |
|---------|--------------|------|------|-------------|
| POST | `/company/create-checkout-session` | Checkoutセッション作成 | 要（事業所） | Company.php |
| GET | `/company/payment-success` | 決済成功ページ | 要（事業所） | Company.php |
| GET | `/company/payment-cancel` | 決済キャンセルページ | 要（事業所） | Company.php |
| GET | `/company/payment-history` | 決済履歴表示 | 要（事業所） | Company.php |
| POST | `/company/create-portal-session` | カスタマーポータル | 要（事業所） | Company.php |
| POST | `/api/stripe/webhook` | Webhook受信 | Stripe署名 | StripeWebhook.php |

### 5.2 詳細仕様

#### 5.2.1 POST /company/create-checkout-session

**目的**: Stripe Checkoutセッションを作成し、ユーザーを決済ページにリダイレクト

**認証**: セッションベース（事業所ユーザーのみ）

**リクエストパラメータ**:
```json
{
  "price_id": "price_xxxxxxxxxxxxx",
  "plan_name": "スタンダードプラン"
}
```

| パラメータ | 型 | 必須 | 説明 |
|-----------|---|------|------|
| price_id | string | ✓ | Stripe Price ID |
| plan_name | string | ✓ | プラン名（表示用） |

**レスポンス（成功時）**:
```json
{
  "success": true,
  "session_id": "cs_test_xxxxxxxxxxxxx",
  "url": "https://checkout.stripe.com/c/pay/cs_test_xxxxxxxxxxxxx"
}
```

**レスポンス（エラー時）**:
```json
{
  "success": false,
  "error": "エラーメッセージ"
}
```

**ステータスコード**:
- 200: 成功
- 400: 不正なリクエスト
- 401: 未認証
- 500: サーバーエラー

**実装例** (application/controllers/Company.php):
```php
public function create_checkout_session()
{
    // 認証チェック
    if (!$this->session->userdata('company_id')) {
        echo json_encode(['success' => false, 'error' => '未認証']);
        return;
    }

    // POSTデータ取得
    $price_id = $this->input->post('price_id');
    $plan_name = $this->input->post('plan_name');

    // バリデーション
    if (empty($price_id) || empty($plan_name)) {
        echo json_encode(['success' => false, 'error' => '必須パラメータが不足しています']);
        return;
    }

    // 事業所情報取得
    $company_id = $this->session->userdata('company_id');
    $this->load->model('Company_model');
    $company = $this->Company_model->get_by_id($company_id);

    if (!$company) {
        echo json_encode(['success' => false, 'error' => '事業所が見つかりません']);
        return;
    }

    // Checkoutセッション作成
    try {
        $this->load->library('Stripe_lib');
        $session = $this->stripe_lib->createCheckoutSession(
            $price_id,
            $company_id,
            $company['email'],
            ['plan_name' => $plan_name]
        );

        echo json_encode([
            'success' => true,
            'session_id' => $session['session_id'],
            'url' => $session['url']
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
}
```

#### 5.2.2 POST /api/stripe/webhook

**目的**: Stripeからのイベントを受信・処理

**認証**: Stripe署名検証（`Stripe-Signature`ヘッダー）

**リクエスト**:
- Content-Type: `application/json`
- ヘッダー: `Stripe-Signature: t=xxx,v1=xxx`
- ボディ: Stripeイベントの生JSONペイロード

**レスポンス**:
- 200: 処理成功
- 400: 署名検証失敗
- 500: 処理エラー

---

## 6. Webhook処理仕様

### 6.1 処理対象イベント

| イベント名 | 説明 | 処理内容 |
|-----------|------|---------|
| `checkout.session.completed` | 決済完了 | 顧客ID・サブスクリプションID保存、有効期限更新 |
| `customer.subscription.created` | サブスクリプション作成 | サブスクリプション情報保存 |
| `customer.subscription.updated` | サブスクリプション更新 | ステータス更新 |
| `customer.subscription.deleted` | サブスクリプションキャンセル | ステータスを`canceled`に更新 |
| `invoice.payment_succeeded` | 支払い成功 | 有効期限延長、決済履歴記録 |
| `invoice.payment_failed` | 支払い失敗 | ステータスを`past_due`に更新、警告 |

### 6.2 Webhookコントローラ実装

#### application/controllers/StripeWebhook.php [新規]

```php
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StripeWebhook extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('Stripe_lib');
        $this->load->model('Payment_model');
        $this->load->model('Webhook_model');
    }

    /**
     * Webhook受信エンドポイント
     */
    public function index()
    {
        // 生のPOSTデータを取得
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            // 署名検証とイベント構築
            $event = $this->stripe_lib->constructWebhookEvent($payload, $sig_header);

            // 冪等性チェック
            if ($this->webhook_model->isEventProcessed($event->id)) {
                log_message('info', 'Event already processed: ' . $event->id);
                http_response_code(200);
                echo json_encode(['received' => true, 'message' => 'Already processed']);
                return;
            }

            // イベントを記録
            $this->webhook_model->recordEvent(
                $event->id,
                $event->type,
                $payload
            );

            // イベントタイプに応じて処理
            switch ($event->type) {
                case 'checkout.session.completed':
                    $this->handleCheckoutCompleted($event);
                    break;

                case 'customer.subscription.created':
                case 'customer.subscription.updated':
                    $this->handleSubscriptionUpdated($event);
                    break;

                case 'customer.subscription.deleted':
                    $this->handleSubscriptionDeleted($event);
                    break;

                case 'invoice.payment_succeeded':
                    $this->handleInvoicePaymentSucceeded($event);
                    break;

                case 'invoice.payment_failed':
                    $this->handleInvoicePaymentFailed($event);
                    break;

                default:
                    log_message('info', 'Unhandled event type: ' . $event->type);
            }

            // 処理済みとしてマーク
            $this->webhook_model->markAsProcessed($event->id);

            http_response_code(200);
            echo json_encode(['received' => true]);

        } catch (Exception $e) {
            log_message('error', 'Webhook error: ' . $e->getMessage());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * checkout.session.completed イベント処理
     */
    private function handleCheckoutCompleted($event)
    {
        $session = $event->data->object;
        $company_id = $session->metadata->company_id ?? null;

        if (!$company_id) {
            log_message('error', 'Company ID not found in metadata');
            return;
        }

        // サブスクリプション情報を取得
        $subscription_id = $session->subscription;
        $customer_id = $session->customer;

        if ($subscription_id) {
            $subscription = $this->stripe_lib->retrieveSubscription($subscription_id);

            // 事業所情報を更新
            $next_billing_date = date('Y-m-d H:i:s', $subscription['current_period_end']);

            $this->payment_model->updateCompanySubscription($company_id, [
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'subscription_status' => 'active',
                'payment_date' => $next_billing_date,
                'subscription_start_date' => date('Y-m-d H:i:s'),
            ]);

            // 決済履歴を記録
            $this->payment_model->recordPayment([
                'company_id' => $company_id,
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'amount' => $subscription['plan_amount'] / 100,
                'currency' => 'jpy',
                'status' => 'succeeded',
                'plan_name' => $session->metadata->plan_name ?? null,
                'plan_interval' => $subscription['plan_interval'],
                'next_billing_date' => $next_billing_date,
                'webhook_event_id' => $event->id,
            ]);
        }

        log_message('info', 'Checkout completed for company: ' . $company_id);
    }

    /**
     * invoice.payment_succeeded イベント処理
     */
    private function handleInvoicePaymentSucceeded($event)
    {
        $invoice = $event->data->object;
        $customer_id = $invoice->customer;
        $subscription_id = $invoice->subscription;

        // 顧客IDから事業所を特定
        $this->load->model('Company_model');
        $company = $this->company_model->get_by_stripe_customer_id($customer_id);

        if (!$company) {
            log_message('error', 'Company not found for customer: ' . $customer_id);
            return;
        }

        if ($subscription_id) {
            $subscription = $this->stripe_lib->retrieveSubscription($subscription_id);
            $next_billing_date = date('Y-m-d H:i:s', $subscription['current_period_end']);

            // 有効期限を延長
            $this->payment_model->updateCompanySubscription($company['id'], [
                'payment_date' => $next_billing_date,
                'subscription_status' => 'active',
            ]);

            // 決済履歴を記録
            $this->payment_model->recordPayment([
                'company_id' => $company['id'],
                'stripe_customer_id' => $customer_id,
                'stripe_subscription_id' => $subscription_id,
                'stripe_invoice_id' => $invoice->id,
                'stripe_payment_intent_id' => $invoice->payment_intent,
                'amount' => $invoice->amount_paid / 100,
                'currency' => $invoice->currency,
                'status' => 'succeeded',
                'plan_interval' => $subscription['plan_interval'],
                'next_billing_date' => $next_billing_date,
                'webhook_event_id' => $event->id,
            ]);
        }

        log_message('info', 'Payment succeeded for company: ' . $company['id']);
    }

    /**
     * invoice.payment_failed イベント処理
     */
    private function handleInvoicePaymentFailed($event)
    {
        $invoice = $event->data->object;
        $customer_id = $invoice->customer;

        // 顧客IDから事業所を特定
        $this->load->model('Company_model');
        $company = $this->company_model->get_by_stripe_customer_id($customer_id);

        if (!$company) {
            return;
        }

        // ステータスを更新
        $this->payment_model->updateCompanySubscription($company['id'], [
            'subscription_status' => 'past_due',
        ]);

        // 失敗履歴を記録
        $this->payment_model->recordPayment([
            'company_id' => $company['id'],
            'stripe_customer_id' => $customer_id,
            'stripe_invoice_id' => $invoice->id,
            'amount' => $invoice->amount_due / 100,
            'currency' => $invoice->currency,
            'status' => 'failed',
            'failure_reason' => $invoice->last_finalization_error->message ?? 'Payment failed',
            'webhook_event_id' => $event->id,
        ]);

        log_message('warning', 'Payment failed for company: ' . $company['id']);
    }

    /**
     * customer.subscription.updated イベント処理
     */
    private function handleSubscriptionUpdated($event)
    {
        $subscription = $event->data->object;
        $customer_id = $subscription->customer;

        $this->load->model('Company_model');
        $company = $this->company_model->get_by_stripe_customer_id($customer_id);

        if (!$company) {
            return;
        }

        // サブスクリプションステータスを更新
        $this->payment_model->updateCompanySubscription($company['id'], [
            'stripe_subscription_id' => $subscription->id,
            'subscription_status' => $subscription->status,
        ]);
    }

    /**
     * customer.subscription.deleted イベント処理
     */
    private function handleSubscriptionDeleted($event)
    {
        $subscription = $event->data->object;
        $customer_id = $subscription->customer;

        $this->load->model('Company_model');
        $company = $this->company_model->get_by_stripe_customer_id($customer_id);

        if (!$company) {
            return;
        }

        // サブスクリプションをキャンセル
        $this->payment_model->updateCompanySubscription($company['id'], [
            'subscription_status' => 'canceled',
            'subscription_end_date' => date('Y-m-d H:i:s'),
        ]);

        log_message('info', 'Subscription canceled for company: ' . $company['id']);
    }
}
```

---

## 7. UI/UX設計

### 7.1 画面一覧

| 画面ID | 画面名 | URL | 目的 | アクセス権限 |
|--------|-------|-----|------|------------|
| PAY-01 | 料金プラン選択 | `/company/payment` | プラン選択・決済 | 事業所 |
| PAY-02 | 決済成功 | `/company/payment-success` | 決済完了確認 | 事業所 |
| PAY-03 | 決済キャンセル | `/company/payment-cancel` | キャンセル通知 | 事業所 |
| PAY-04 | 決済履歴 | `/company/payment-history` | 決済履歴表示 | 事業所 |
| PAY-05 | 期限切れ警告 | `/company/payment-expired` | 有効期限切れ警告 | 事業所 |

### 7.2 画面遷移図

```
[ログイン]
    │
    ▼
[有効期限チェック]
    │
    ├─[有効期限内]─→ [ダッシュボード]
    │                      │
    │                      ▼
    │                 [料金プラン選択] (PAY-01)
    │                      │
    │                      ├─[プラン選択]
    │                      │     ▼
    │                      │ [Stripe Checkout]
    │                      │     │
    │                      │     ├─[決済成功]─→ [決済成功ページ] (PAY-02)
    │                      │     │                    ▼
    │                      │     │              [ダッシュボード]
    │                      │     │
    │                      │     └─[キャンセル]─→ [決済キャンセル] (PAY-03)
    │                      │                          ▼
    │                      │                    [料金プラン選択]
    │                      │
    │                      └─[履歴表示]─→ [決済履歴] (PAY-04)
    │
    └─[期限切れ]─→ [期限切れ警告] (PAY-05)
                        │
                        └─[決済へ]─→ [料金プラン選択]
```

### 7.3 各画面の詳細設計

#### 7.3.1 PAY-01: 料金プラン選択画面

**ファイル**: `application/views/company/payment.php`

**レイアウト**:
```
┌────────────────────────────────────────────────────────┐
│ ヘッダー: DayCare.app - 料金プラン                        │
├────────────────────────────────────────────────────────┤
│                                                        │
│  現在の契約情報                                          │
│  ┌────────────────────────────────────────────────┐   │
│  │ プラン: スタンダードプラン                          │   │
│  │ ステータス: [有効] 🟢                              │   │
│  │ 有効期限: 2025-11-01                              │   │
│  │ 次回請求日: 2025-11-01                            │   │
│  └────────────────────────────────────────────────┘   │
│                                                        │
│  ┌──────────────────────────────┐                    │
│  │  [決済履歴を見る]              │                    │
│  └──────────────────────────────┘                    │
│                                                        │
│  料金プラン一覧                                          │
│  ┌────────────────────────────────────────────────┐   │
│  │  Stripe Pricing Table                          │   │
│  │  (Stripeが提供する埋め込みウィジェット)              │   │
│  │                                                  │   │
│  │  [スタンダードプラン]    [プレミアムプラン]          │   │
│  │   ¥5,000/月              ¥10,000/月             │   │
│  │   - 機能A               - 機能A                   │   │
│  │   - 機能B               - 機能B                   │   │
│  │   [選択]                - 機能C                   │   │
│  │                         [選択]                    │   │
│  └────────────────────────────────────────────────┘   │
│                                                        │
└────────────────────────────────────────────────────────┘
```

**HTMLコード例**:
```php
<!-- application/views/company/payment.php -->
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>料金プラン - DayCare.app</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <script src="https://js.stripe.com/v3/pricing-table.js"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>料金プラン</h1>

                <!-- 現在の契約情報 -->
                <?php if (!empty($subscription)): ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">現在の契約情報</h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>プラン:</strong>
                                <?= htmlspecialchars($subscription['subscription_plan'] ?? 'なし') ?>
                            </div>
                            <div class="col-md-3">
                                <strong>ステータス:</strong>
                                <?php
                                $status = $subscription['subscription_status'];
                                $status_label = '';
                                $status_class = '';
                                switch ($status) {
                                    case 'active':
                                        $status_label = '有効';
                                        $status_class = 'label-success';
                                        break;
                                    case 'past_due':
                                        $status_label = '支払い遅延';
                                        $status_class = 'label-warning';
                                        break;
                                    case 'canceled':
                                        $status_label = 'キャンセル済み';
                                        $status_class = 'label-danger';
                                        break;
                                    default:
                                        $status_label = '未契約';
                                        $status_class = 'label-default';
                                }
                                ?>
                                <span class="label <?= $status_class ?>">
                                    <?= $status_label ?>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <strong>有効期限:</strong>
                                <?= date('Y年m月d日', strtotime($subscription['payment_date'])) ?>
                            </div>
                            <div class="col-md-3">
                                <a href="<?= base_url('company/payment-history') ?>" class="btn btn-info btn-sm">
                                    <i class="fa fa-history"></i> 決済履歴を見る
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Stripe Pricing Table -->
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">料金プラン一覧</h3>
                    </div>
                    <div class="panel-body">
                        <stripe-pricing-table
                            pricing-table-id="<?= $this->config->item('stripe_pricing_table_id') ?>"
                            publishable-key="<?= $this->config->item('stripe_publishable_key') ?>"
                            client-reference-id="<?= $company_id ?>"
                            customer-email="<?= htmlspecialchars($company_email) ?>">
                        </stripe-pricing-table>
                    </div>
                </div>

                <!-- 注意事項 -->
                <div class="alert alert-info">
                    <h4><i class="fa fa-info-circle"></i> ご注意</h4>
                    <ul>
                        <li>決済はStripeの安全な決済ページで行われます</li>
                        <li>カード情報は当社サーバーには保存されません</li>
                        <li>サブスクリプションは自動更新されます</li>
                        <li>キャンセルはいつでも可能です</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
</body>
</html>
```

**コントローラメソッド** (Company.php):
```php
public function payment()
{
    // 認証チェック
    if (!$this->session->userdata('company_id')) {
        redirect('login');
    }

    $company_id = $this->session->userdata('company_id');

    // 事業所情報取得
    $this->load->model('Company_model');
    $company = $this->Company_model->get_by_id($company_id);

    // ビューに渡すデータ
    $data = [
        'company_id' => $company_id,
        'company_email' => $company['email'],
        'subscription' => [
            'subscription_plan' => $company['subscription_plan'],
            'subscription_status' => $company['subscription_status'],
            'payment_date' => $company['payment_date'],
        ],
    ];

    $this->load->view('company/payment', $data);
}
```

#### 7.3.2 PAY-02: 決済成功画面

**ファイル**: `application/views/company/payment_success.php`

**HTMLコード例**:
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済完了 - DayCare.app</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
    <style>
        .success-icon {
            font-size: 80px;
            color: #5cb85c;
            margin: 30px 0;
        }
        .success-message {
            text-align: center;
            padding: 50px 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">決済完了</h3>
                    </div>
                    <div class="panel-body success-message">
                        <div class="success-icon">
                            <i class="fa fa-check-circle"></i>
                        </div>
                        <h2>お支払いが完了しました！</h2>
                        <p class="lead">ご契約ありがとうございます。</p>

                        <?php if (!empty($subscription_info)): ?>
                        <div class="well text-left" style="margin-top: 30px;">
                            <h4>契約情報</h4>
                            <dl class="dl-horizontal">
                                <dt>プラン:</dt>
                                <dd><?= htmlspecialchars($subscription_info['plan_name']) ?></dd>

                                <dt>金額:</dt>
                                <dd>¥<?= number_format($subscription_info['amount']) ?> / <?= $subscription_info['interval'] === 'month' ? '月' : '年' ?></dd>

                                <dt>有効期限:</dt>
                                <dd><?= date('Y年m月d日', strtotime($subscription_info['next_billing_date'])) ?></dd>

                                <dt>次回請求日:</dt>
                                <dd><?= date('Y年m月d日', strtotime($subscription_info['next_billing_date'])) ?></dd>
                            </dl>
                        </div>
                        <?php endif; ?>

                        <div style="margin-top: 30px;">
                            <a href="<?= base_url('company/dashboard') ?>" class="btn btn-primary btn-lg">
                                <i class="fa fa-home"></i> ダッシュボードへ
                            </a>
                            <a href="<?= base_url('company/payment-history') ?>" class="btn btn-default btn-lg">
                                <i class="fa fa-history"></i> 決済履歴を見る
                            </a>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <h4><i class="fa fa-envelope"></i> 確認メールを送信しました</h4>
                    <p>ご登録のメールアドレスに決済完了の確認メールを送信しました。メールが届かない場合は、迷惑メールフォルダをご確認ください。</p>
                </div>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
</body>
</html>
```

#### 7.3.3 PAY-04: 決済履歴画面

**ファイル**: `application/views/company/payment_history.php`

**HTMLコード例**:
```php
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>決済履歴 - DayCare.app</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css') ?>">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1>決済履歴</h1>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="fa fa-history"></i> 決済履歴一覧
                        </h3>
                    </div>
                    <div class="panel-body">
                        <?php if (empty($payments)): ?>
                        <p class="text-center text-muted">決済履歴がありません。</p>
                        <?php else: ?>
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>決済日</th>
                                    <th>プラン</th>
                                    <th>金額</th>
                                    <th>ステータス</th>
                                    <th>次回請求日</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td><?= date('Y/m/d H:i', strtotime($payment['payment_date'])) ?></td>
                                    <td><?= htmlspecialchars($payment['plan_name'] ?? '-') ?></td>
                                    <td>¥<?= number_format($payment['amount']) ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch ($payment['status']) {
                                            case 'succeeded':
                                                $status_class = 'label-success';
                                                $status_text = '成功';
                                                break;
                                            case 'failed':
                                                $status_class = 'label-danger';
                                                $status_text = '失敗';
                                                break;
                                            case 'pending':
                                                $status_class = 'label-warning';
                                                $status_text = '処理中';
                                                break;
                                            default:
                                                $status_class = 'label-default';
                                                $status_text = $payment['status'];
                                        }
                                        ?>
                                        <span class="label <?= $status_class ?>">
                                            <?= $status_text ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $payment['next_billing_date'] ? date('Y/m/d', strtotime($payment['next_billing_date'])) : '-' ?>
                                    </td>
                                    <td>
                                        <?php if ($payment['stripe_invoice_id']): ?>
                                        <a href="<?= base_url('company/download-invoice/' . $payment['id']) ?>"
                                           class="btn btn-xs btn-default"
                                           target="_blank">
                                            <i class="fa fa-download"></i> 領収書
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                        <!-- ページネーション -->
                        <?php if ($total_pages > 1): ?>
                        <nav>
                            <ul class="pagination">
                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <li class="<?= $i == $current_page ? 'active' : '' ?>">
                                    <a href="<?= base_url('company/payment-history?page=' . $i) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                                <?php endfor; ?>
                            </ul>
                        </nav>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?= base_url('company/payment') ?>" class="btn btn-default">
                    <i class="fa fa-arrow-left"></i> 料金プランへ戻る
                </a>
            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/js/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/bootstrap.min.js') ?>"></script>
</body>
</html>
```

### 7.4 レスポンシブデザイン対応

**ブレークポイント**:
- デスクトップ: 1200px以上
- タブレット: 768px - 1199px
- モバイル: 767px以下

**対応内容**:
- Bootstrapのグリッドシステムを使用
- Stripe Pricing Tableは自動的にレスポンシブ
- テーブルは横スクロール可能に

---

## 8. セキュリティ実装

### 8.1 Webhook署名検証

**実装済み**: `Stripe_lib.php` の `constructWebhookEvent()` メソッド

**検証フロー**:
```php
// 1. Stripe署名ヘッダーを取得
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

// 2. 生のPOSTデータを取得（重要: パース前の生データ）
$payload = file_get_contents('php://input');

// 3. Stripe SDKで署名検証
$event = \Stripe\Webhook::constructEvent(
    $payload,
    $sig_header,
    $webhook_secret
);

// 4. 検証成功 → イベント処理
// 5. 検証失敗 → 400エラーを返す
```

### 8.2 CSRF保護

**CodeIgniterのCSRF設定** (`application/config/config.php`):
```php
$config['csrf_protection'] = TRUE;
$config['csrf_token_name'] = 'csrf_test_name';
$config['csrf_cookie_name'] = 'csrf_cookie_name';
$config['csrf_expire'] = 7200;
$config['csrf_regenerate'] = TRUE;
$config['csrf_exclude_uris'] = array('api/stripe/webhook'); // Webhook除外
```

**フォームでのCSRF対策**:
```php
<form method="POST" action="<?= base_url('company/create-checkout-session') ?>">
    <?php echo $this->security->get_csrf_hash(); ?>
    <input type="hidden" name="<?= $this->security->get_csrf_token_name() ?>"
           value="<?= $this->security->get_csrf_hash() ?>">
    <!-- その他のフィールド -->
</form>
```

### 8.3 XSS対策

**出力エスケープ**:
```php
// すべてのユーザー入力データをエスケープ
<?= htmlspecialchars($user_input, ENT_QUOTES, 'UTF-8') ?>

// CodeIgniterのヘルパー使用
<?= html_escape($user_input) ?>
```

### 8.4 SQLインジェクション対策

**Query Builderを使用**:
```php
// ❌ 危険
$sql = "SELECT * FROM tbl_company WHERE id = " . $_GET['id'];

// ✅ 安全
$this->db->where('id', $id);
$query = $this->db->get('tbl_company');

// ✅ プリペアドステートメント
$sql = "SELECT * FROM tbl_company WHERE id = ?";
$query = $this->db->query($sql, array($id));
```

### 8.5 APIキー管理

**環境変数での管理** (.env ファイル):
```bash
# .env
STRIPE_TEST_SECRET_KEY=sk_test_xxxxxxxxxxxxx
STRIPE_TEST_PUBLISHABLE_KEY=pk_test_xxxxxxxxxxxxx
STRIPE_LIVE_SECRET_KEY=sk_live_xxxxxxxxxxxxx
STRIPE_LIVE_PUBLISHABLE_KEY=pk_live_xxxxxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

**PHP環境変数取得**:
```php
$config['stripe_live_secret_key'] = getenv('STRIPE_LIVE_SECRET_KEY');
```

**.gitignore に追加**:
```
.env
application/config/stripe_config.php
```

---

## 9. テスト仕様

### 9.1 単体テスト

#### 9.1.1 Stripe_lib のテスト

**ファイル**: `tests/libraries/Stripe_lib_test.php`

```php
<?php
use PHPUnit\Framework\TestCase;

class Stripe_lib_test extends TestCase
{
    protected $CI;
    protected $stripe_lib;

    protected function setUp(): void
    {
        // CodeIgniterのインスタンス取得
        $this->CI = &get_instance();
        $this->CI->load->library('Stripe_lib');
        $this->stripe_lib = $this->CI->stripe_lib;
    }

    /**
     * Checkoutセッション作成のテスト
     */
    public function testCreateCheckoutSession()
    {
        $price_id = 'price_test_xxxxx';
        $company_id = 1;
        $company_email = 'test@example.com';

        $result = $this->stripe_lib->createCheckoutSession(
            $price_id,
            $company_id,
            $company_email
        );

        // セッションIDとURLが返されることを確認
        $this->assertArrayHasKey('session_id', $result);
        $this->assertArrayHasKey('url', $result);
        $this->assertStringStartsWith('cs_', $result['session_id']);
        $this->assertStringContainsString('checkout.stripe.com', $result['url']);
    }

    /**
     * 無効なAPIキーでエラーが発生するかテスト
     */
    public function testInvalidApiKey()
    {
        $this->expectException(Exception::class);

        // 無効なAPIキーを設定
        \Stripe\Stripe::setApiKey('sk_test_invalid');

        $this->stripe_lib->createCheckoutSession(
            'price_invalid',
            1,
            'test@example.com'
        );
    }

    /**
     * Webhook署名検証のテスト
     */
    public function testWebhookSignatureVerification()
    {
        $payload = '{"id": "evt_test", "type": "checkout.session.completed"}';
        $signature = 'valid_signature'; // テスト用の署名

        // 正しい署名で検証成功
        $event = $this->stripe_lib->constructWebhookEvent($payload, $signature);
        $this->assertNotNull($event);
    }

    /**
     * 無効な署名でエラーが発生するかテスト
     */
    public function testInvalidWebhookSignature()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('署名検証失敗');

        $payload = '{"id": "evt_test"}';
        $invalid_signature = 'invalid_signature';

        $this->stripe_lib->constructWebhookEvent($payload, $invalid_signature);
    }
}
```

#### 9.1.2 Payment_model のテスト

```php
<?php
use PHPUnit\Framework\TestCase;

class Payment_model_test extends TestCase
{
    protected $CI;
    protected $payment_model;

    protected function setUp(): void
    {
        $this->CI = &get_instance();
        $this->CI->load->model('Payment_model');
        $this->payment_model = $this->CI->Payment_model;
    }

    /**
     * 決済履歴記録のテスト
     */
    public function testRecordPayment()
    {
        $payment_data = [
            'company_id' => 1,
            'stripe_customer_id' => 'cus_test123',
            'stripe_subscription_id' => 'sub_test123',
            'amount' => 5000,
            'currency' => 'jpy',
            'status' => 'succeeded',
            'plan_name' => 'スタンダードプラン',
            'plan_interval' => 'month',
            'next_billing_date' => date('Y-m-d H:i:s', strtotime('+1 month')),
        ];

        $payment_id = $this->payment_model->recordPayment($payment_data);

        // IDが返されることを確認
        $this->assertGreaterThan(0, $payment_id);

        // データベースに保存されたか確認
        $saved_payment = $this->payment_model->getLatestPayment(1);
        $this->assertEquals('succeeded', $saved_payment['status']);
        $this->assertEquals(5000, $saved_payment['amount']);
    }

    /**
     * 決済履歴取得のテスト
     */
    public function testGetPaymentHistory()
    {
        $company_id = 1;
        $history = $this->payment_model->getPaymentHistory($company_id, 10);

        $this->assertIsArray($history);

        if (!empty($history)) {
            $this->assertArrayHasKey('company_id', $history[0]);
            $this->assertArrayHasKey('amount', $history[0]);
            $this->assertArrayHasKey('status', $history[0]);
        }
    }
}
```

### 9.2 統合テスト

#### 9.2.1 決済フロー統合テスト

**テストシナリオ1: 新規決済の成功フロー**

```php
<?php
use PHPUnit\Framework\TestCase;

class PaymentFlowTest extends TestCase
{
    /**
     * 新規決済の完全フローテスト
     */
    public function testNewPaymentFlow()
    {
        // 1. テスト事業所でログイン
        $this->loginAsCompany(1);

        // 2. 料金プランページにアクセス
        $response = $this->get('/company/payment');
        $this->assertEquals(200, $response->getStatusCode());

        // 3. Checkoutセッション作成
        $response = $this->post('/company/create-checkout-session', [
            'price_id' => 'price_test_xxxxx',
            'plan_name' => 'スタンダードプラン',
        ]);

        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('session_id', $data);

        // 4. Webhookシミュレーション（checkout.session.completed）
        $webhook_payload = $this->createWebhookPayload('checkout.session.completed', [
            'customer' => 'cus_test123',
            'subscription' => 'sub_test123',
            'metadata' => ['company_id' => 1, 'plan_name' => 'スタンダードプラン'],
        ]);

        $response = $this->postWebhook('/api/stripe/webhook', $webhook_payload);
        $this->assertEquals(200, $response->getStatusCode());

        // 5. データベース検証
        $company = $this->getCompany(1);
        $this->assertEquals('cus_test123', $company['stripe_customer_id']);
        $this->assertEquals('sub_test123', $company['stripe_subscription_id']);
        $this->assertEquals('active', $company['subscription_status']);

        // 6. 決済履歴が記録されているか確認
        $payments = $this->getPaymentHistory(1);
        $this->assertCount(1, $payments);
        $this->assertEquals('succeeded', $payments[0]['status']);
    }
}
```

### 9.3 E2Eテスト（Stripe CLIを使用）

**Stripe CLIでのWebhookテスト**:

```bash
# Stripe CLIでローカルにWebhookを転送
stripe listen --forward-to http://localhost/api/stripe/webhook

# テストイベントを送信
stripe trigger checkout.session.completed

# 決済成功イベント
stripe trigger invoice.payment_succeeded

# 決済失敗イベント
stripe trigger invoice.payment_failed

# サブスクリプションキャンセル
stripe trigger customer.subscription.deleted
```

**テストカード番号**:
- 成功: `4242 4242 4242 4242`
- 拒否: `4000 0000 0000 0002`
- 3Dセキュア認証: `4000 0027 6000 3184`

---

## 10. デプロイ手順

### 10.1 事前準備

#### 10.1.1 Stripeアカウント設定

1. **Stripeアカウント作成**
   - https://dashboard.stripe.com/register にアクセス
   - アカウント登録（本人確認必要）

2. **料金プラン作成**
   ```
   Stripe Dashboard → 商品 → 新規作成
   - プラン名: スタンダードプラン
   - 価格: ¥5,000
   - 請求間隔: 月次
   ```

3. **Pricing Table作成**
   ```
   Stripe Dashboard → 決済リンク → Pricing Table
   - テーブルを作成
   - プランを追加
   - Pricing Table IDをコピー（prctbl_xxxxx）
   ```

4. **APIキー取得**
   ```
   Stripe Dashboard → 開発者 → APIキー
   - テストモード公開可能キー (pk_test_xxxxx)
   - テストモードシークレットキー (sk_test_xxxxx)
   ```

#### 10.1.2 環境変数設定

**.env ファイル作成** (本番サーバー):
```bash
# Stripe設定
STRIPE_ENVIRONMENT=live
STRIPE_LIVE_PUBLISHABLE_KEY=pk_live_xxxxxxxxxxxxx
STRIPE_LIVE_SECRET_KEY=sk_live_xxxxxxxxxxxxx
STRIPE_LIVE_WEBHOOK_SECRET=whsec_xxxxxxxxxxxxx
```

### 10.2 データベースマイグレーション

**手順**:

1. **バックアップ作成**
```bash
mysqldump -u root -p database_name > backup_$(date +%Y%m%d_%H%M%S).sql
```

2. **マイグレーションSQL実行**
```bash
mysql -u root -p database_name < application/db/stripe_tables.sql
```

**マイグレーションSQLファイル** (`application/db/stripe_tables.sql`):
```sql
-- tbl_company テーブル拡張
ALTER TABLE `tbl_company`
ADD COLUMN `stripe_customer_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe顧客ID' AFTER `payment_date`,
ADD COLUMN `stripe_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeサブスクリプションID' AFTER `stripe_customer_id`,
ADD COLUMN `subscription_status` VARCHAR(50) DEFAULT 'inactive' COMMENT 'サブスクリプションステータス' AFTER `stripe_subscription_id`,
ADD COLUMN `subscription_plan` VARCHAR(100) DEFAULT NULL COMMENT 'プラン名' AFTER `subscription_status`,
ADD COLUMN `subscription_start_date` DATETIME DEFAULT NULL COMMENT 'サブスクリプション開始日' AFTER `subscription_plan`,
ADD COLUMN `subscription_end_date` DATETIME DEFAULT NULL COMMENT 'サブスクリプション終了日' AFTER `subscription_start_date`,
ADD INDEX `idx_stripe_customer` (`stripe_customer_id`),
ADD INDEX `idx_subscription_status` (`subscription_status`);

-- tbl_payment_history テーブル作成
CREATE TABLE IF NOT EXISTS `tbl_payment_history` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT '決済履歴ID',
  `company_id` INT(11) NOT NULL COMMENT '事業所ID',
  `stripe_customer_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe顧客ID',
  `stripe_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeサブスクリプションID',
  `stripe_invoice_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe請求書ID',
  `stripe_payment_intent_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe PaymentIntent ID',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '決済金額',
  `currency` VARCHAR(3) DEFAULT 'jpy' COMMENT '通貨コード',
  `status` VARCHAR(50) NOT NULL COMMENT '決済ステータス',
  `plan_name` VARCHAR(255) DEFAULT NULL COMMENT 'プラン名',
  `plan_interval` VARCHAR(20) DEFAULT NULL COMMENT '請求間隔',
  `payment_date` DATETIME NOT NULL COMMENT '決済日時',
  `next_billing_date` DATETIME DEFAULT NULL COMMENT '次回請求日',
  `webhook_event_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeイベントID',
  `failure_reason` TEXT DEFAULT NULL COMMENT '失敗理由',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時',
  INDEX `idx_company_id` (`company_id`),
  INDEX `idx_stripe_customer` (`stripe_customer_id`),
  INDEX `idx_stripe_subscription` (`stripe_subscription_id`),
  INDEX `idx_payment_date` (`payment_date`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='決済履歴テーブル';

-- tbl_stripe_webhooks テーブル作成
CREATE TABLE IF NOT EXISTS `tbl_stripe_webhooks` (
  `id` INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY COMMENT 'Webhook記録ID',
  `event_id` VARCHAR(255) UNIQUE NOT NULL COMMENT 'StripeイベントID',
  `event_type` VARCHAR(100) NOT NULL COMMENT 'イベントタイプ',
  `processed` TINYINT(1) DEFAULT 0 COMMENT '処理済みフラグ',
  `payload` LONGTEXT DEFAULT NULL COMMENT 'イベントペイロード',
  `processing_result` TEXT DEFAULT NULL COMMENT '処理結果',
  `received_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '受信日時',
  `processed_at` DATETIME DEFAULT NULL COMMENT '処理完了日時',
  INDEX `idx_event_id` (`event_id`),
  INDEX `idx_event_type` (`event_type`),
  INDEX `idx_processed` (`processed`),
  INDEX `idx_received_at` (`received_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Webhook受信管理テーブル';
```

3. **ロールバックSQL準備**
```sql
-- ロールバック用SQL
ALTER TABLE `tbl_company`
DROP COLUMN `stripe_customer_id`,
DROP COLUMN `stripe_subscription_id`,
DROP COLUMN `subscription_status`,
DROP COLUMN `subscription_plan`,
DROP COLUMN `subscription_start_date`,
DROP COLUMN `subscription_end_date`;

DROP TABLE IF EXISTS `tbl_payment_history`;
DROP TABLE IF EXISTS `tbl_stripe_webhooks`;
```

### 10.3 ファイルデプロイ

**新規ファイル配置**:
```bash
# ライブラリ
welfare/application/libraries/Stripe_lib.php

# モデル
welfare/application/models/Payment_model.php
welfare/application/models/Webhook_model.php

# コントローラ
welfare/application/controllers/StripeWebhook.php
# welfare/application/controllers/Company.php (変更)

# ビュー
welfare/application/views/company/payment.php
welfare/application/views/company/payment_success.php
welfare/application/views/company/payment_cancel.php
welfare/application/views/company/payment_history.php

# 設定
welfare/application/config/stripe_config.php
```

### 10.4 Webhook URL登録

**Stripe Dashboardで設定**:
```
1. Stripe Dashboard → 開発者 → Webhook
2. エンドポイントを追加
   - URL: https://your-domain.com/api/stripe/webhook
   - リスンするイベント:
     ✓ checkout.session.completed
     ✓ customer.subscription.created
     ✓ customer.subscription.updated
     ✓ customer.subscription.deleted
     ✓ invoice.payment_succeeded
     ✓ invoice.payment_failed
3. Webhook署名シークレットをコピー (whsec_xxxxx)
```

### 10.5 動作確認

**チェックリスト**:
```
□ データベースマイグレーション完了
□ 設定ファイル配置完了（APIキー設定済み）
□ Webhook URL登録完了
□ 料金プランページ表示確認
□ Pricing Table表示確認
□ テスト決済実行（テストカード）
□ Webhook受信確認
□ データベース更新確認
□ 決済履歴表示確認
□ 有効期限チェック動作確認
```

---

## 11. 運用・保守

### 11.1 ログ監視

**ログファイル**:
- Stripeログ: `application/logs/stripe_YYYY-MM-DD.log`
- CodeIgniterログ: `application/logs/log-YYYY-MM-DD.php`

**監視コマンド**:
```bash
# Stripeログをリアルタイム監視
tail -f application/logs/stripe_$(date +%Y-%m-%d).log

# エラーのみ抽出
grep -i "error" application/logs/stripe_*.log

# 決済失敗を抽出
grep "Payment failed" application/logs/stripe_*.log
```

### 11.2 トラブルシューティング

#### 問題1: Webhookが受信されない

**症状**: 決済後、データベースが更新されない

**原因**:
- Webhook URLが間違っている
- ファイアウォールでブロックされている
- SSL証明書エラー

**対処法**:
```bash
# 1. Webhook URLを確認
# Stripe Dashboard → 開発者 → Webhook

# 2. ログを確認
tail -f application/logs/stripe_*.log

# 3. Stripe CLIでテスト
stripe listen --forward-to https://your-domain.com/api/stripe/webhook
stripe trigger checkout.session.completed

# 4. 署名検証を一時的に無効化してテスト（本番ではNG）
```

#### 問題2: 決済は成功したが有効期限が更新されない

**症状**: Stripe Dashboardでは決済成功だが、tbl_company.payment_dateが更新されない

**原因**:
- Webhookイベントの処理でエラー発生
- company_idのメタデータが不正

**対処法**:
```sql
-- tbl_stripe_webhooksを確認
SELECT * FROM tbl_stripe_webhooks
WHERE processed = 0
ORDER BY received_at DESC
LIMIT 10;

-- 未処理イベントがある場合は手動で再処理
-- または処理結果を確認
SELECT event_id, event_type, processing_result
FROM tbl_stripe_webhooks
WHERE processing_result LIKE '%error%';
```

### 11.3 バックアップ戦略

**データベースバックアップ**:
```bash
# 日次バックアップ（cronで自動化）
0 2 * * * mysqldump -u root -p database_name > /backups/db_$(date +\%Y\%m\%d).sql

# 決済関連テーブルのみバックアップ
mysqldump -u root -p database_name tbl_payment_history tbl_stripe_webhooks tbl_company > payment_backup.sql
```

**ログローテーション**:
```bash
# 30日以上古いログを削除
find application/logs/stripe_*.log -mtime +30 -delete
```

### 11.4 定期メンテナンス

**月次タスク**:
```sql
-- 1. Webhookイベントの処理状況確認
SELECT
    event_type,
    COUNT(*) as total,
    SUM(processed) as processed_count
FROM tbl_stripe_webhooks
WHERE received_at >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
GROUP BY event_type;

-- 2. 決済成功率の確認
SELECT
    DATE_FORMAT(payment_date, '%Y-%m') as month,
    COUNT(*) as total_payments,
    SUM(CASE WHEN status = 'succeeded' THEN 1 ELSE 0 END) as successful,
    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
FROM tbl_payment_history
GROUP BY DATE_FORMAT(payment_date, '%Y-%m')
ORDER BY month DESC;

-- 3. 有効期限切れ間近の事業所
SELECT id, company_name, payment_date, subscription_status
FROM tbl_company
WHERE payment_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)
AND subscription_status = 'active';
```

---

## 12. 付録

### 12.1 エラーコード一覧

| コード | 説明 | 対処法 |
|-------|------|--------|
| STRIPE_001 | APIキーが無効 | 設定ファイルを確認 |
| STRIPE_002 | 署名検証失敗 | Webhook Secretを確認 |
| STRIPE_003 | 顧客が見つからない | データベースを確認 |
| STRIPE_004 | 決済処理エラー | Stripeダッシュボードを確認 |
| STRIPE_005 | Webhook処理エラー | ログを確認 |

### 12.2 参考リンク

- [Stripe API ドキュメント](https://stripe.com/docs/api)
- [Stripe PHP SDK](https://github.com/stripe/stripe-php)
- [Stripe Webhooks ガイド](https://stripe.com/docs/webhooks)
- [CodeIgniter 3 ユーザーガイド](https://codeigniter.com/userguide3/)

---

**文書作成者**: Development Team
**最終更新日**: 2025-10-01
**バージョン**: 1.0
**承認者**: _________________