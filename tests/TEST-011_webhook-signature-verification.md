# TEST-011: Webhook署名検証テスト

**テストID**: TEST-011
**テスト種別**: セキュリティテスト
**所要時間**: 約30分
**前提条件**: Webhook実装が完了していること

---

## 1. テスト目的

StripeからのWebhookリクエストが正しい署名を持っている場合のみ処理され、不正なリクエストが拒否されることを確認する。これにより、第三者による不正なWebhookリクエストを防止する。

---

## 2. テスト環境

- **Webhook URL**: http://localhost/DayCare.app/welfare/stripe_webhook
- **データベース**: daycare_welfare
- **ツール**: curl, Postman, またはStripe CLI

---

## 3. セキュリティの重要性

### 3.1 なぜ署名検証が必要か

- ✅ 第三者による不正なWebhookリクエストを防ぐ
- ✅ データの改ざんを検知する
- ✅ 正当なStripeからのリクエストのみを処理する

### 3.2 署名検証の仕組み

1. StripeがWebhookを送信する際、`Stripe-Signature`ヘッダーに署名を付与
2. サーバー側でWebhook Secretを使って署名を検証
3. 署名が一致しない場合はリクエストを拒否

---

## 4. 事前準備

### 4.1 Webhook Secretの確認

```php
// stripe_config.phpを開いて、Webhook Secretを確認
$config['stripe_test_webhook_secret'] = 'whsec_xxxxxxxxxxxxxxxxxxxxx';
```

この値を記録しておく → **Webhook Secret**: ______

### 4.2 テスト用データベースのバックアップ

```bash
# バックアップを作成（念のため）
mysqldump -u root daycare_welfare > backup_before_webhook_test.sql
```

---

## 5. テスト手順

### テストケース1: 正当な署名（Stripe CLIを使用）

#### ステップ1: Stripe CLIでWebhookをトリガー

**⚠️ 注意**: ローカル環境ではCSRF保護の問題でWebhookが受信できない場合があります。その場合は、本番環境またはステージング環境でテストしてください。

```bash
# Stripe CLIでテストイベントを送信
stripe trigger checkout.session.completed
```

**期待結果**:
- ✅ Stripe CLIが成功メッセージを表示
- ✅ Webhookエンドポイントがリクエストを受信
- ✅ 署名検証が成功
- ✅ イベントが正常に処理される

#### ステップ2: ログファイル確認

```bash
# Stripeログファイルを確認
tail -f C:\xampp\htdocs\DayCare.app\welfare\application\logs\stripe_*.log
```

**期待結果**:
- ✅ 「Webhook signature verified successfully」のようなログが記録されている
- ✅ イベント処理のログが記録されている
- ✅ エラーログがない

#### ステップ3: データベース確認

```sql
SELECT event_id, event_type, processed
FROM tbl_stripe_webhooks
ORDER BY received_at DESC
LIMIT 1;
```

**期待結果**:
- ✅ 新しいWebhookイベントが記録されている
- ✅ `processed` が `1`

---

### テストケース2: 不正な署名（curlを使用）

#### ステップ1: 不正な署名でリクエスト送信

以下のコマンドでWebhookエンドポイントに不正なリクエストを送信：

```bash
curl -X POST http://localhost/DayCare.app/welfare/stripe_webhook \
  -H "Content-Type: application/json" \
  -H "Stripe-Signature: t=1234567890,v1=invalid_signature_here,v0=another_invalid" \
  -d '{
    "id": "evt_fake_event_12345",
    "object": "event",
    "type": "checkout.session.completed",
    "data": {
      "object": {
        "id": "cs_test_fake123",
        "customer": "cus_fake123"
      }
    }
  }'
```

**期待結果**:
- ✅ HTTPステータス: `400 Bad Request` または `403 Forbidden`
- ✅ エラーメッセージが返される（例: "Invalid signature"）
- ✅ Webhookが処理されない

**レスポンス例**:
```json
{
  "success": false,
  "error": "Webhook signature verification failed"
}
```

#### ステップ2: ログファイル確認

```bash
tail -n 20 C:\xampp\htdocs\DayCare.app\welfare\application\logs\stripe_*.log
```

**期待結果**:
- ✅ 「Webhook signature verification failed」のようなログが記録されている
- ✅ エラーレベルのログとして記録されている
- ✅ 不正なリクエストの詳細が記録されている

#### ステップ3: データベース確認

```sql
-- 不正なイベントが記録されていないことを確認
SELECT COUNT(*) AS invalid_event_count
FROM tbl_stripe_webhooks
WHERE event_id = 'evt_fake_event_12345';
```

**期待結果**:
- ✅ `invalid_event_count` が `0`
- ✅ 不正なイベントが記録されていない

---

### テストケース3: 署名ヘッダーなし

#### ステップ1: Stripe-Signatureヘッダーなしでリクエスト送信

```bash
curl -X POST http://localhost/DayCare.app/welfare/stripe_webhook \
  -H "Content-Type: application/json" \
  -d '{
    "id": "evt_no_signature_123",
    "object": "event",
    "type": "invoice.payment_succeeded"
  }'
```

**期待結果**:
- ✅ HTTPステータス: `400 Bad Request`
- ✅ エラーメッセージ: "Missing Stripe-Signature header" または類似のメッセージ
- ✅ Webhookが処理されない

#### ステップ2: ログ確認

**期待結果**:
- ✅ 「Missing Stripe-Signature header」のログが記録されている

---

### テストケース4: 古いタイムスタンプ（リプレイ攻撃の防止）

#### ステップ1: 過去のタイムスタンプで署名を生成

Stripeは、タイムスタンプが5分以上古いリクエストを拒否します。

```bash
# 古いタイムスタンプ（5分以上前）でリクエスト送信
# 注意: 署名の生成には実際のWebhook Secretが必要
curl -X POST http://localhost/DayCare.app/welfare/stripe_webhook \
  -H "Content-Type: application/json" \
  -H "Stripe-Signature: t=1500000000,v1=old_signature_here" \
  -d '{
    "id": "evt_old_timestamp_123",
    "object": "event",
    "type": "customer.subscription.updated"
  }'
```

**期待結果**:
- ✅ HTTPステータス: `400 Bad Request`
- ✅ エラーメッセージ: "Timestamp too old" または類似のメッセージ
- ✅ リプレイ攻撃が防止される

---

### テストケース5: 改ざんされたペイロード

#### ステップ1: 正当な署名だが改ざんされたペイロードを送信

実際のStripeからのWebhookを傍受し、ペイロードの一部を改ざんしてから再送信する攻撃を想定。

**期待結果**:
- ✅ 署名検証が失敗する
- ✅ HTTPステータス: `400 Bad Request`
- ✅ 改ざんが検知される

---

## 6. コード確認

### 6.1 StripeWebhook.phpの署名検証コード確認

以下のコードが実装されていることを確認：

```php
// application/controllers/StripeWebhook.php

public function index()
{
    try {
        // 署名ヘッダーを取得
        $signature = $this->input->get_request_header('Stripe-Signature');

        if (empty($signature)) {
            log_message('error', 'Webhook: Missing Stripe-Signature header');
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'error' => 'Missing signature header'
                ]));
            return;
        }

        // リクエストボディを取得
        $payload = file_get_contents('php://input');

        // 署名を検証してイベントを構築
        $event = $this->stripe_lib->constructWebhookEvent(
            $payload,
            $signature
        );

        // イベント処理...

    } catch (\Stripe\Exception\SignatureVerificationException $e) {
        // 署名検証失敗
        log_message('error', 'Webhook signature verification failed: ' . $e->getMessage());
        $this->output
            ->set_status_header(400)
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'success' => false,
                'error' => 'Invalid signature'
            ]));
        return;
    }
}
```

**確認結果**: ✅ 実装されている / ❌ 実装されていない

---

## 7. テスト結果記録

### 7.1 テスト実施情報

| 項目 | 内容 |
|------|------|
| テスト実施日 | YYYY-MM-DD |
| テスト実施者 | [名前] |
| 実施環境 | ローカル / ステージング / 本番 |
| 所要時間 | XX分 |

### 7.2 テスト結果サマリー

| テストケース | 結果 | 備考 |
|------------|------|------|
| ケース1: 正当な署名（Stripe CLI） | ✅ PASS / ❌ FAIL |  |
| ケース2: 不正な署名 | ✅ PASS / ❌ FAIL |  |
| ケース3: 署名ヘッダーなし | ✅ PASS / ❌ FAIL |  |
| ケース4: 古いタイムスタンプ | ✅ PASS / ❌ FAIL |  |
| ケース5: 改ざんされたペイロード | ✅ PASS / ❌ FAIL |  |

### 7.3 総合判定

**判定**: ⭕ 合格 / ❌ 不合格

---

## 8. セキュリティ評価

### 8.1 脆弱性チェック

以下の脆弱性が**ない**ことを確認：

- ❌ 署名検証のバイパス
- ❌ タイミング攻撃への脆弱性
- ❌ リプレイ攻撃への脆弱性
- ❌ ペイロード改ざんの検知漏れ

**評価**: ⭕ セキュア / ❌ 脆弱性あり

### 8.2 推奨事項

- ✅ Webhook Secretを環境変数で管理する
- ✅ 本番環境ではHTTPSを必須にする
- ✅ ログに機密情報を記録しない
- ✅ エラーレスポンスで内部情報を漏らさない

---

## 9. トラブルシューティング

### 9.1 「CSRF token mismatch」エラーが出る場合

**原因**: Webhookエンドポイントが CSRF保護の対象になっている

**解決策**:
```php
// application/config/config.php
$config['csrf_exclude_uris'] = array('stripe_webhook', 'stripe_webhook/.*');
```

### 9.2 「Webhook signature verification failed」が常に出る場合

**原因**: Webhook Secretが正しく設定されていない

**解決策**:
1. Stripe Dashboardで正しいWebhook Secretを確認
2. `stripe_config.php`の`stripe_test_webhook_secret`を更新
3. 環境（test/live）が一致しているか確認

---

## 10. 参考資料

- [Stripe Webhook Security](https://stripe.com/docs/webhooks/signatures)
- [Stripe PHP SDK - Webhook Verification](https://github.com/stripe/stripe-php#webhook-signing)
- OWASP - Webhook Security Best Practices

---

**テスト手順書作成日**: 2025-10-03
**作成者**: Claude (AI Assistant)
**バージョン**: 1.0
