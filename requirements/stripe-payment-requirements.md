# Stripe決済機能 要件書

**プロジェクト名**: DayCare.app - 訪問看護・福祉事業所管理システム
**作成日**: 2025-10-01
**バージョン**: 1.0
**対象システム**: CodeIgniter 3.x (PHP) + Stripe PHP SDK v17.2

---

## 1. 概要

### 1.1 目的
訪問看護・福祉事業所向け管理システムに、Stripe決済機能を統合し、サブスクリプションベースの課金モデルを実装する。事業所（company）が利用プランを購入・更新し、有効期限に基づいてシステムへのアクセスを制御する。

### 1.2 背景
- 既存システムには `payment.php` ビューが存在し、Stripe Pricing Tableの実装が部分的に完了している
- `composer.json` に `stripe/stripe-php: ^17.2` が既にインストール済み
- データベース（`tbl_company`）に `payment_date` カラムが存在し、有効期限の管理が可能
- 現在の実装は表示のみで、決済処理や自動更新の仕組みが未実装

### 1.3 対象ユーザー
- **プライマリユーザー**: 事業所管理者（Company管理者）
- **セカンダリユーザー**: システム管理者（Admin）- 決済状況の監視と管理
- **対象外**: スタッフユーザー・利用者ユーザー（決済機能にはアクセス不可）

### 1.4 スコープ
**含まれる機能**:
- Stripe Pricing Tableを使った料金プラン表示
- サブスクリプション決済処理
- Webhookによる決済イベントの受信と処理
- 有効期限ベースのアクセス制御
- 決済履歴の記録と表示
- サブスクリプションの管理（更新・キャンセル）

**含まれない機能**:
- カスタマーサポートチャット機能
- 返金処理（初期フェーズでは手動対応）
- 請求書の自動発行（Stripe側の機能に委ねる）
- 複数通貨対応（JPYのみ）

---

## 2. 機能要件

### 2.1 必須機能

#### 2.1.1 Stripe設定管理
- **FR-001**: Stripe APIキー（Publishable Key / Secret Key）を設定ファイルで管理
  - テスト環境と本番環境の切り替えが可能
  - 設定項目: `publishable_key`, `secret_key`, `webhook_secret`
  - 設定ファイル: `application/config/config.php` または独立した `stripe_config.php`

#### 2.1.2 料金プラン表示
- **FR-002**: 事業所管理画面（`/company/payment`）に料金プラン表示
  - Stripe Pricing Table埋め込み
  - 複数プラン対応（例: スタンダードプラン、プレミアムプラン）
  - 現在の契約プラン・有効期限を表示
  - 有効/満了ステータスの視覚的表示（ラベル色分け）

#### 2.1.3 決済処理（サブスクリプション）
- **FR-003**: Stripe Checkoutセッションによる決済処理
  - 事業所情報（company_id, company_email）とStripe顧客情報の紐付け
  - 決済成功後のリダイレクト処理
  - 決済キャンセル時の処理

#### 2.1.4 Webhook処理
- **FR-004**: Stripeからのイベントを受信・処理するWebhookエンドポイント
  - エンドポイント: `/api/stripe/webhook` または `/webhook/stripe`
  - 署名検証による不正アクセス防止
  - 処理対象イベント:
    - `checkout.session.completed` - 決済完了
    - `customer.subscription.created` - サブスクリプション作成
    - `customer.subscription.updated` - サブスクリプション更新
    - `customer.subscription.deleted` - サブスクリプションキャンセル
    - `invoice.payment_succeeded` - 支払い成功（定期更新）
    - `invoice.payment_failed` - 支払い失敗
  - 冪等性保証（同一イベントの重複処理防止）

#### 2.1.5 有効期限管理
- **FR-005**: 決済完了時に `tbl_company.payment_date` を更新
  - サブスクリプション作成時: 次回請求日を設定
  - 更新成功時: 有効期限を延長
  - 支払い失敗時: 有効期限を変更せず、警告通知

#### 2.1.6 アクセス制御
- **FR-006**: 有効期限切れ事業所のシステムアクセス制限
  - ログイン時に有効期限をチェック
  - 有効期限切れの場合、専用の支払いページへリダイレクト
  - システム管理者（Admin）は制限対象外
  - 猶予期間の設定（例: 期限切れ後7日間は警告のみ）

#### 2.1.7 決済履歴管理
- **FR-007**: 決済履歴を記録・表示
  - 新規テーブル: `tbl_payment_history`
    ```sql
    CREATE TABLE `tbl_payment_history` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `company_id` INT NOT NULL,
      `stripe_customer_id` VARCHAR(255),
      `stripe_subscription_id` VARCHAR(255),
      `stripe_invoice_id` VARCHAR(255),
      `amount` DECIMAL(10,2) NOT NULL,
      `currency` VARCHAR(3) DEFAULT 'jpy',
      `status` VARCHAR(50) NOT NULL,
      `plan_name` VARCHAR(255),
      `payment_date` DATETIME NOT NULL,
      `next_billing_date` DATETIME,
      `webhook_event_id` VARCHAR(255),
      `created_at` DATETIME NOT NULL,
      `updated_at` DATETIME NOT NULL,
      INDEX `idx_company_id` (`company_id`),
      INDEX `idx_stripe_customer` (`stripe_customer_id`)
    );
    ```
  - 管理画面での履歴表示（事業所・管理者）

#### 2.1.8 エラーハンドリング
- **FR-008**: 決済エラー時の適切な処理
  - カード拒否時のユーザーフレンドリーなエラーメッセージ
  - Stripe API通信エラー時のフォールバック処理
  - エラーログの記録（`application/logs/stripe_*.log`）

### 2.2 オプション機能（将来的実装）

#### 2.2.1 カスタマーポータル
- **FR-OPT-001**: Stripe Customer Portalへのリンク提供
  - 事業所が自分で請求書をダウンロード
  - クレジットカード情報の更新
  - サブスクリプションのキャンセル

#### 2.2.2 メール通知
- **FR-OPT-002**: 決済イベントに基づくメール通知
  - 決済成功メール
  - 支払い失敗警告メール
  - 有効期限接近通知（7日前、3日前、当日）
  - サブスクリプションキャンセル確認メール

#### 2.2.3 管理者ダッシュボード
- **FR-OPT-003**: システム管理者向け決済状況ダッシュボード
  - 全事業所の決済ステータス一覧
  - 売上レポート
  - 支払い失敗事業所のリスト
  - サブスクリプション統計

#### 2.2.4 トライアル期間
- **FR-OPT-004**: 新規登録時の無料トライアル機能
  - 14日間無料トライアル
  - トライアル期間中の機能制限なし
  - トライアル終了前の通知

---

## 3. 非機能要件

### 3.1 パフォーマンス要件
- **NFR-001**: Webhook処理は5秒以内に完了すること（Stripe推奨）
- **NFR-002**: 決済ページの初回ロードは3秒以内
- **NFR-003**: 有効期限チェックはキャッシュ機構を利用し、ログイン時のオーバーヘッドを最小化

### 3.2 セキュリティ要件
- **NFR-004**: Stripe APIキーは環境変数または暗号化された設定ファイルで管理
- **NFR-005**: Webhook署名検証を必須とし、未検証のリクエストは拒否
- **NFR-006**: PCI DSS準拠 - カード情報はサーバー側で一切保持しない（Stripeに委譲）
- **NFR-007**: SSL/TLS必須（本番環境）
- **NFR-008**: CSRF保護（CodeIgniterのCSRF機能を有効化）
- **NFR-009**: SQLインジェクション対策（CodeIgniter Query Builderを使用）
- **NFR-010**: XSS対策（入出力のエスケープ処理）

### 3.3 可用性要件
- **NFR-011**: Stripe APIが一時的にダウンしても、既存ユーザーのシステムアクセスは継続可能
- **NFR-012**: Webhookイベントの処理失敗時は再試行可能な設計（Stripeが自動再送信）
- **NFR-013**: 稼働率: 99.5%以上（Stripeの稼働率に依存）

### 3.4 ユーザビリティ要件
- **NFR-014**: 決済フローは3ステップ以内（プラン選択 → 決済情報入力 → 完了）
- **NFR-015**: レスポンシブデザイン対応（モバイル・タブレット・PC）
- **NFR-016**: 日本語メッセージ（エラーメッセージ含む）
- **NFR-017**: 決済完了後の明確なフィードバック（成功メッセージ、次回請求日表示）

### 3.5 互換性要件
- **NFR-018**: PHP 7.4以上（Stripe PHP SDK要件）
- **NFR-019**: MySQL 5.7以上またはMariaDB 10.2以上
- **NFR-020**: ブラウザ対応: Chrome, Firefox, Safari, Edge（最新版＋1世代前）

### 3.6 保守性・拡張性要件
- **NFR-021**: ログ記録（決済処理、Webhookイベント、エラー）
- **NFR-022**: コードのモジュール化（Stripe処理をライブラリ化）
- **NFR-023**: 設定値の外部化（ハードコードを避ける）
- **NFR-024**: ユニットテスト可能な設計

---

## 4. 技術仕様

### 4.1 使用技術

#### 4.1.1 決済プラットフォーム
- **Stripe**: v2023-10-16以降のAPIバージョン
- **Stripe PHP SDK**: v17.2（既にインストール済み）
- **Stripe Pricing Table**: Stripe Dashboardで作成・管理
- **Stripe Checkout**: ホスティング型決済ページ

#### 4.1.2 フレームワーク・ライブラリ
- **Backend**: CodeIgniter 3.x
- **Database**: MySQL/MariaDB
- **Frontend**: 既存のAdminLTE + Bootstrap 3.x

#### 4.1.3 開発環境
- **ローカル環境**: XAMPP (Apache + MySQL + PHP 7.4+)
- **Stripeテストモード**: テストキーを使用

### 4.2 データベース設計

#### 4.2.1 既存テーブルの拡張
**tbl_company** に以下のカラムを追加（既に `payment_date` は存在）:
```sql
ALTER TABLE `tbl_company`
ADD COLUMN `stripe_customer_id` VARCHAR(255) DEFAULT NULL COMMENT 'Stripe顧客ID',
ADD COLUMN `stripe_subscription_id` VARCHAR(255) DEFAULT NULL COMMENT 'StripeサブスクリプションID',
ADD COLUMN `subscription_status` VARCHAR(50) DEFAULT 'inactive' COMMENT 'active, inactive, past_due, canceled',
ADD COLUMN `subscription_plan` VARCHAR(100) DEFAULT NULL COMMENT 'プラン名',
ADD INDEX `idx_stripe_customer` (`stripe_customer_id`);
```

#### 4.2.2 新規テーブル
**tbl_payment_history** (前述のFR-007を参照)

**tbl_stripe_webhooks** - Webhook処理の冪等性保証用:
```sql
CREATE TABLE `tbl_stripe_webhooks` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `event_id` VARCHAR(255) UNIQUE NOT NULL COMMENT 'StripeイベントID',
  `event_type` VARCHAR(100) NOT NULL,
  `processed` TINYINT(1) DEFAULT 0,
  `payload` TEXT,
  `received_at` DATETIME NOT NULL,
  `processed_at` DATETIME,
  INDEX `idx_event_id` (`event_id`)
);
```

### 4.3 API仕様

#### 4.3.1 決済処理エンドポイント

**1. Checkoutセッション作成**
- **URL**: `POST /company/create-checkout-session`
- **認証**: ログイン必須（事業所ユーザー）
- **リクエストパラメータ**:
  ```json
  {
    "price_id": "price_xxxxxxxxxxxxx",
    "plan_name": "スタンダードプラン"
  }
  ```
- **レスポンス**:
  ```json
  {
    "session_id": "cs_xxxxxxxxxxxxx",
    "url": "https://checkout.stripe.com/c/pay/cs_xxxxxxxxxxxxx"
  }
  ```
- **エラー**:
  - 401: 未認証
  - 400: 無効なprice_id
  - 500: Stripe API エラー

**2. Webhook受信**
- **URL**: `POST /api/stripe/webhook` または `POST /webhook/stripe`
- **認証**: Stripe署名検証
- **リクエスト**: Stripeからの生のJSONペイロード
- **レスポンス**:
  - 200: 処理成功
  - 400: 署名検証失敗
  - 500: 処理エラー

**3. 決済履歴取得**
- **URL**: `GET /company/payment-history`
- **認証**: ログイン必須
- **レスポンス**:
  ```json
  [
    {
      "id": 1,
      "amount": 5000,
      "currency": "jpy",
      "status": "succeeded",
      "plan_name": "スタンダードプラン",
      "payment_date": "2025-01-15 10:30:00",
      "next_billing_date": "2025-02-15 10:30:00"
    }
  ]
  ```

#### 4.3.2 カスタマーポータルセッション作成（オプション）
- **URL**: `POST /company/create-portal-session`
- **認証**: ログイン必須
- **レスポンス**:
  ```json
  {
    "url": "https://billing.stripe.com/session/xxxxxxxxxxxxx"
  }
  ```

### 4.4 ファイル構成

```
DayCare.app/welfare/
├── application/
│   ├── config/
│   │   ├── stripe_config.php          # Stripe設定ファイル（新規）
│   │   └── config.php                 # 既存設定ファイルにStripe設定追加
│   ├── controllers/
│   │   ├── Company.php                # 既存：決済ページ表示を追加
│   │   ├── StripeWebhook.php          # 新規：Webhook処理
│   │   └── Api.php                    # 既存：APIエンドポイント追加
│   ├── models/
│   │   ├── Company_model.php          # 既存：拡張
│   │   ├── Payment_model.php          # 新規：決済履歴管理
│   │   └── Stripe_model.php           # 新規：Stripe API操作
│   ├── libraries/
│   │   └── Stripe_lib.php             # 新規：Stripe操作ライブラリ
│   ├── views/
│   │   └── company/
│   │       ├── payment.php            # 既存：拡張
│   │       ├── payment_success.php    # 新規：決済成功ページ
│   │       ├── payment_cancel.php     # 新規：決済キャンセルページ
│   │       └── payment_history.php    # 新規：決済履歴ページ
│   └── db/
│       └── stripe_tables.sql          # 新規：テーブル定義SQL
├── composer.json                      # stripe/stripe-php は既存
└── vendor/                            # Stripe SDK
```

### 4.5 主要クラス設計

#### 4.5.1 Stripe_lib.php（ライブラリ）
```php
class Stripe_lib {
    private $stripe;

    public function __construct() {
        // Stripe SDK初期化
    }

    public function createCheckoutSession($priceId, $companyId, $companyEmail) {
        // Checkoutセッション作成
    }

    public function createCustomer($companyEmail, $companyName) {
        // Stripe顧客作成
    }

    public function retrieveSubscription($subscriptionId) {
        // サブスクリプション情報取得
    }

    public function createPortalSession($customerId) {
        // カスタマーポータルセッション作成
    }
}
```

#### 4.5.2 StripeWebhook.php（コントローラー）
```php
class StripeWebhook extends CI_Controller {
    public function index() {
        // Webhook受信・署名検証
        // イベントタイプに応じた処理振り分け
    }

    private function handleCheckoutCompleted($event) {
        // checkout.session.completed 処理
    }

    private function handleInvoicePaymentSucceeded($event) {
        // invoice.payment_succeeded 処理
    }

    private function handleSubscriptionUpdated($event) {
        // customer.subscription.updated 処理
    }
}
```

#### 4.5.3 Payment_model.php（モデル）
```php
class Payment_model extends Base_model {
    public function __construct() {
        parent::__construct();
        $this->table = 'tbl_payment_history';
    }

    public function recordPayment($data) {
        // 決済履歴を記録
    }

    public function getPaymentHistory($companyId, $limit = 10) {
        // 決済履歴取得
    }

    public function updateCompanySubscription($companyId, $subscriptionData) {
        // 事業所のサブスクリプション情報を更新
    }
}
```

---

## 5. 制約条件

### 5.1 技術的制約
- **CONS-001**: Stripe PHP SDKはPHP 7.4以上が必須（現環境の確認必要）
- **CONS-002**: Webhookエンドポイントは公開アクセス可能なURLが必要（ローカル開発時はngrokなど使用）
- **CONS-003**: SSL証明書が必要（本番環境）
- **CONS-004**: CodeIgniter 3.xの制約に従う（RESTful APIのサポートが限定的）

### 5.2 セキュリティ制約
- **CONS-005**: PCI DSS準拠のため、カード情報は一切サーバーで扱わない
- **CONS-006**: Webhook署名検証は必須（スキップ不可）
- **CONS-007**: APIキーは環境変数または暗号化された設定ファイルのみ
- **CONS-008**: 本番環境ではStripe本番キーを使用（テストキー厳禁）

### 5.3 ビジネス制約
- **CONS-009**: 決済通貨はJPY（日本円）のみ
- **CONS-010**: Stripeの手数料: 3.6%（サブスクリプション決済の場合）
- **CONS-011**: サブスクリプション最小金額: 50円（Stripe制限）
- **CONS-012**: 返金はStripe Dashboardから手動で行う（初期フェーズ）

### 5.4 期限・リソース
- **CONS-013**: 実装期限: 未定（要調整）
- **CONS-014**: 開発リソース: 1名（要確認）
- **CONS-015**: テスト期間: 最低2週間（決済機能のため慎重なテストが必要）

---

## 6. 実装の優先順位

### フェーズ1: 最優先（MVP - Minimum Viable Product）
**目標**: 基本的な決済フローを実装し、サブスクリプションの作成と有効期限管理を実現

1. **Stripe設定管理** (FR-001)
   - 設定ファイルの作成
   - APIキーの管理

2. **料金プラン表示** (FR-002)
   - payment.phpビューの完成（既存コードを活用）
   - Stripe Pricing Table統合

3. **決済処理** (FR-003)
   - Checkoutセッション作成
   - 成功/キャンセルページ

4. **Webhook基本処理** (FR-004)
   - Webhookエンドポイント作成
   - `checkout.session.completed` 処理
   - `invoice.payment_succeeded` 処理

5. **有効期限管理** (FR-005, FR-006)
   - `payment_date` 更新ロジック
   - ログイン時の有効期限チェック
   - 有効期限切れ時のアクセス制限

6. **データベース設計** (4.2)
   - `tbl_company` 拡張
   - `tbl_payment_history` 作成
   - `tbl_stripe_webhooks` 作成

7. **基本的なエラーハンドリング** (FR-008)

**完了基準**:
- 事業所がプランを購入できる
- 決済成功時に有効期限が更新される
- 有効期限切れの事業所はログインできない

### フェーズ2: 次点（機能拡張）
**目標**: 管理機能と履歴管理を追加

1. **決済履歴管理** (FR-007)
   - 履歴記録機能
   - 履歴表示ページ

2. **Webhook拡張処理** (FR-004)
   - `customer.subscription.updated` 処理
   - `customer.subscription.deleted` 処理
   - `invoice.payment_failed` 処理

3. **エラーハンドリング強化** (FR-008)
   - 詳細なログ記録
   - ユーザーフレンドリーなエラーメッセージ

4. **管理者機能** (FR-OPT-003 部分)
   - 全事業所の決済ステータス表示
   - 支払い失敗事業所リスト

**完了基準**:
- 事業所が決済履歴を確認できる
- 管理者が全体の決済状況を把握できる
- 支払い失敗時の処理が適切に行われる

### フェーズ3: 将来的（拡張機能）
**目標**: UX向上と自動化

1. **カスタマーポータル** (FR-OPT-001)
   - Stripe Customer Portalへのリンク
   - カード情報更新機能

2. **メール通知** (FR-OPT-002)
   - 決済成功メール
   - 支払い失敗警告メール
   - 有効期限接近通知

3. **トライアル期間** (FR-OPT-004)
   - 新規登録時の無料トライアル
   - トライアル終了通知

4. **管理者ダッシュボード拡張** (FR-OPT-003)
   - 売上レポート
   - サブスクリプション統計
   - グラフ・チャート表示

5. **自動テストの実装**
   - ユニットテスト
   - Webhookのモックテスト

**完了基準**:
- 事業所が自分でサブスクリプションを管理できる
- 自動通知により手動での連絡が不要
- 管理者が詳細なレポートを確認できる

---

## 7. テスト計画

### 7.1 テスト環境
- **Stripeテストモード**: テストキーを使用
- **テストカード番号**:
  - 成功: 4242 4242 4242 4242
  - 拒否: 4000 0000 0000 0002
  - 認証必要: 4000 0027 6000 3184
- **Webhook テストツール**: Stripe CLI (`stripe listen --forward-to`)

### 7.2 単体テスト項目

#### 7.2.1 Stripe_lib.php
- [ ] `createCheckoutSession()` が正しいセッションを作成する
- [ ] `createCustomer()` がStripe顧客を作成する
- [ ] 無効なAPIキーでエラーが発生する
- [ ] ネットワークエラー時に適切に例外処理される

#### 7.2.2 Payment_model.php
- [ ] `recordPayment()` がデータベースに正しく記録する
- [ ] `getPaymentHistory()` が正しい履歴を取得する
- [ ] `updateCompanySubscription()` が事業所情報を更新する

#### 7.2.3 StripeWebhook.php
- [ ] 署名検証が正しく動作する
- [ ] 無効な署名でリクエストが拒否される
- [ ] 各イベントタイプが正しく処理される
- [ ] 重複イベントが無視される（冪等性）

### 7.3 統合テスト項目

#### 7.3.1 決済フロー（成功ケース）
- [ ] 事業所が料金プランページにアクセスできる
- [ ] プラン選択後、Stripe Checkoutにリダイレクトされる
- [ ] テストカード（4242...）で決済完了する
- [ ] 決済成功ページにリダイレクトされる
- [ ] Webhookが受信され、処理される
- [ ] `tbl_company.payment_date` が更新される
- [ ] `tbl_payment_history` にレコードが追加される
- [ ] 事業所がシステムにアクセス可能になる

#### 7.3.2 決済フロー（失敗ケース）
- [ ] カード拒否（4000...0002）時にエラーメッセージが表示される
- [ ] 決済キャンセル時にキャンセルページにリダイレクトされる
- [ ] 決済失敗時に `payment_date` が変更されない
- [ ] エラーログが記録される

#### 7.3.3 Webhook処理
- [ ] `checkout.session.completed` イベント処理
- [ ] `invoice.payment_succeeded` イベント処理（更新時）
- [ ] `invoice.payment_failed` イベント処理
- [ ] `customer.subscription.deleted` イベント処理
- [ ] 重複イベントが2回処理されない

#### 7.3.4 有効期限とアクセス制御
- [ ] 有効期限内の事業所がログインできる
- [ ] 有効期限切れの事業所がログインできない
- [ ] 有効期限切れ後、決済ページにリダイレクトされる
- [ ] 管理者は有効期限に関係なくアクセスできる

### 7.4 決済テスト環境（Stripeテストモード）
**実施内容**:
1. 新規サブスクリプション作成テスト
2. サブスクリプション更新テスト（月次更新のシミュレーション）
3. カード情報更新テスト
4. サブスクリプションキャンセルテスト
5. 支払い失敗テスト

**テストシナリオ例**:
```
シナリオ1: 新規事業所の初回決済
1. テスト事業所でログイン
2. 決済ページにアクセス
3. スタンダードプランを選択
4. Stripe Checkoutでテストカード入力（4242...）
5. 決済完了を確認
6. データベースを確認（payment_date, stripe_customer_id, subscription_status）
7. 決済履歴ページで履歴を確認

シナリオ2: 有効期限切れ事業所のアクセス制限
1. データベースで payment_date を過去日付に変更
2. テスト事業所でログイン試行
3. 決済ページにリダイレクトされることを確認
4. システムの他のページにアクセスできないことを確認
```

### 7.5 負荷テスト（オプション）
- Webhookエンドポイントの同時リクエスト処理
- 大量の決済履歴データ取得時のパフォーマンス

---

## 8. リスクと対策

### 8.1 技術的リスク

#### リスク1: Webhook処理の遅延・失敗
- **影響度**: 高
- **発生確率**: 中
- **内容**: Webhookイベントの処理が失敗し、データベースが更新されない
- **対策**:
  - Stripe側の自動再送信機能を活用（24時間以内に複数回再送信）
  - 冪等性を保証し、重複処理を防止
  - Webhook処理のログを詳細に記録
  - 手動での再処理機能を実装（管理者向け）

#### リスク2: Stripe APIの一時的なダウン
- **影響度**: 中
- **発生確率**: 低
- **内容**: Stripe APIが利用できず、決済処理ができない
- **対策**:
  - ユーザーに再試行を促すメッセージ表示
  - 既存ユーザーのアクセスは継続可能な設計
  - Stripeのステータスページを監視

#### リスク3: データベースと決済状態の不整合
- **影響度**: 高
- **発生確率**: 低
- **内容**: Webhookは成功したがDB更新が失敗、または逆のケース
- **対策**:
  - トランザクション処理を使用
  - Stripe APIから最新情報を取得する同期機能
  - 管理者による手動修正機能

### 8.2 セキュリティリスク

#### リスク4: Webhook署名検証の不備
- **影響度**: 高
- **発生確率**: 低（実装次第）
- **内容**: 署名検証が不十分で不正なリクエストを受け付けてしまう
- **対策**:
  - Stripe SDKの署名検証機能を必ず使用
  - 検証失敗時は即座にリクエストを拒否
  - ログに不正アクセス試行を記録

#### リスク5: APIキーの漏洩
- **影響度**: 致命的
- **発生確率**: 低
- **内容**: Stripe APIキーが外部に漏洩し、不正利用される
- **対策**:
  - APIキーは環境変数で管理
  - Gitリポジトリにコミットしない（.gitignoreに追加）
  - 定期的なキーのローテーション
  - Stripeダッシュボードで異常なアクティビティを監視

### 8.3 ビジネスリスク

#### リスク6: 決済失敗率の増加
- **影響度**: 中
- **発生確率**: 中
- **内容**: カード期限切れ等で決済が失敗し、事業所がシステムを利用できなくなる
- **対策**:
  - 決済失敗時のメール通知（フェーズ3）
  - 猶予期間の設定（7日間）
  - Stripe Smart Retryの活用（自動再試行）

#### リスク7: 料金プラン変更時の混乱
- **影響度**: 中
- **発生確率**: 中
- **内容**: 既存顧客のプラン変更時にトラブルが発生
- **対策**:
  - Stripe Dashboardで料金プランを慎重に管理
  - 既存顧客への通知プロセス確立
  - 移行期間の設定

### 8.4 運用リスク

#### リスク8: 決済トラブル時のサポート対応
- **影響度**: 中
- **発生確率**: 中
- **内容**: 決済に関する問い合わせに適切に対応できない
- **対策**:
  - FAQ・ヘルプページの整備
  - サポート担当者向けマニュアル作成
  - Stripeダッシュボードへのアクセス権管理

#### リスク9: 法規制・税制の変更
- **影響度**: 中
- **発生確率**: 低
- **内容**: 電子商取引に関する法規制の変更により対応が必要
- **対策**:
  - 定期的な法規制の確認
  - Stripeの日本対応状況を監視
  - 弁護士・会計士への相談体制

---

## 9. 参考資料

### 9.1 Stripe公式ドキュメント
- **Stripe API Reference**: https://stripe.com/docs/api
- **Stripe PHP SDK**: https://github.com/stripe/stripe-php
- **Stripe Checkout**: https://stripe.com/docs/payments/checkout
- **Stripe Webhooks**: https://stripe.com/docs/webhooks
- **Stripe Testing**: https://stripe.com/docs/testing

### 9.2 CodeIgniter関連
- **CodeIgniter 3 User Guide**: https://codeigniter.com/userguide3/
- **CodeIgniter Security**: https://codeigniter.com/userguide3/general/security.html

### 9.3 セキュリティ基準
- **PCI DSS Overview**: https://www.pcisecuritystandards.org/

---

## 10. 承認・レビュー

### 10.1 要件確認項目
- [ ] 本要件書の内容は正確か
- [ ] 既存システムとの整合性は取れているか
- [ ] 技術的な実現可能性は確認済みか
- [ ] セキュリティ要件は十分か
- [ ] 期限・リソースは妥当か

### 10.2 承認者
- **プロジェクトマネージャー**: ___________________ 日付: ___________
- **技術リード**: ___________________ 日付: ___________
- **事業責任者**: ___________________ 日付: ___________

---

## 11. 変更履歴

| バージョン | 日付 | 変更者 | 変更内容 |
|-----------|------|--------|----------|
| 1.0 | 2025-10-01 | Claude | 初版作成 |

---

## 12. 次のステップ

### 12.1 仕様書作成への移行
本要件書が承認された後、以下の詳細仕様書を作成することを推奨します:

1. **技術仕様書（Technical Specification）**
   - 詳細なクラス設計
   - APIエンドポイントの詳細仕様
   - データベースの正規化とインデックス戦略
   - エラーコード一覧

2. **画面仕様書（UI Specification）**
   - ワイヤーフレーム
   - 画面遷移図
   - ユーザーフロー図

3. **テスト仕様書（Test Specification）**
   - 詳細なテストケース
   - テストデータ
   - テスト環境構築手順

4. **運用マニュアル（Operations Manual）**
   - デプロイ手順
   - 監視項目
   - トラブルシューティングガイド

### 12.2 実装前の準備
- [ ] Stripe アカウントの作成（テストモード）
- [ ] 料金プランの設定（Stripe Dashboard）
- [ ] Pricing Table の作成
- [ ] Webhook エンドポイントの設定
- [ ] ローカル開発環境でのStripe CLI設定
- [ ] GitHubリポジトリのブランチ作成（feature/stripe-payment）

### 12.3 推奨される開発順序
1. データベーステーブル作成
2. Stripe設定ファイルとライブラリの実装
3. Checkoutセッション作成機能
4. 決済ページの実装
5. Webhook処理の実装
6. 有効期限チェック機能
7. 決済履歴機能
8. テスト実施
9. 本番デプロイ

---

**要件書作成者**: Claude (AI Assistant)
**作成日**: 2025-10-01
**プロジェクト**: DayCare.app - Stripe決済機能統合
