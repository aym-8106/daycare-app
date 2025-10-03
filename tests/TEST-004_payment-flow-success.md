# TEST-004: 決済フロー統合テスト（成功ケース）

**テストID**: TEST-004
**テスト種別**: 統合テスト（E2E）
**所要時間**: 約20分
**前提条件**: Phase 1-3の実装が完了していること

---

## 1. テスト目的

新規事業所がStripe Pricing Tableからプランを選択し、決済が成功するまでの一連のフローが正常に動作することを確認する。

---

## 2. テスト環境

- **URL**: http://localhost/DayCare.app/welfare/
- **データベース**: daycare_welfare (MySQL/MariaDB)
- **Stripeモード**: テストモード
- **テストカード番号**: 4242 4242 4242 4242
- **有効期限**: 任意の未来の日付（例: 12/34）
- **CVC**: 任意の3桁数字（例: 123）
- **郵便番号**: 任意（例: 12345）

---

## 3. 事前準備

### 3.1 テストデータの準備

#### データベースで事業所を確認/作成

```sql
-- テスト用事業所の確認
SELECT company_id, company_name, company_email, stripe_customer_id, subscription_status
FROM tbl_company
WHERE company_id = 1;

-- もし存在しない場合は、管理画面から事業所を作成するか、
-- ログイン可能な既存事業所を使用してください
```

### 3.2 Stripe Pricing Tableの確認

Stripe Dashboardで以下を確認：
- Pricing Table IDが設定されている
- 2つ以上のプラン（スタンダード、プレミアム等）が存在する
- テストモードで動作している

### 3.3 ブラウザの準備

- ブラウザのキャッシュをクリア
- シークレットモード/プライベートブラウジングモードを使用（推奨）
- 開発者ツールのコンソールを開いておく（エラー確認用）

---

## 4. テスト手順

### ステップ1: ログイン

1. ブラウザで `http://localhost/DayCare.app/welfare/` にアクセス
2. 事業所アカウントでログイン
   - ユーザーID: （テスト用アカウント）
   - パスワード: （テスト用パスワード）

**期待結果**:
- ✅ ログイン成功
- ✅ ダッシュボード画面が表示される

---

### ステップ2: 料金プランページへ移動

1. サイドメニューまたはURLバーから `/company/payment` にアクセス
   - URL: `http://localhost/DayCare.app/welfare/company/payment`

**期待結果**:
- ✅ 「料金プラン」ページが表示される
- ✅ Stripe Pricing Tableが正しく表示される
- ✅ 2つ以上のプランが表示される
- ✅ 各プランに価格、機能説明が表示される

**スクリーンショット**: `screenshots/test-004-step2.png` として保存

---

### ステップ3: プラン選択

1. Pricing Tableから「スタンダードプラン」または「プレミアムプラン」のボタンをクリック
2. Stripe Checkoutページへリダイレクトされるのを待つ

**期待結果**:
- ✅ ローディングインジケーターが表示される（FE-011実装済み）
- ✅ 数秒以内にStripe Checkoutページ（https://checkout.stripe.com）へリダイレクトされる
- ✅ Checkoutページに選択したプランの情報が表示される
- ✅ 金額が正しく表示される

**スクリーンショット**: `screenshots/test-004-step3.png`

---

### ステップ4: 決済情報入力

Stripe Checkoutページで以下を入力：

1. **メールアドレス**: test@example.com（任意のテスト用メール）
2. **カード番号**: `4242 4242 4242 4242`
3. **有効期限**: `12/34`（任意の未来の日付）
4. **CVC**: `123`（任意の3桁数字）
5. **カード名義**: `Test User`
6. **郵便番号**: `12345`（任意）

**期待結果**:
- ✅ 全ての入力フィールドが正しく表示される
- ✅ バリデーションエラーが表示されない
- ✅ 「申し込む」ボタンがクリック可能になる

---

### ステップ5: 決済実行

1. 「申し込む」または「Subscribe」ボタンをクリック
2. 処理が完了するまで待つ（数秒～10秒程度）

**期待結果**:
- ✅ 処理中のローディングインジケーターが表示される
- ✅ エラーメッセージが表示されない
- ✅ 決済が成功する

---

### ステップ6: 決済成功ページの確認

決済完了後、アプリケーションの決済成功ページへリダイレクトされる

**期待結果**:
- ✅ `/company/payment-success` ページへリダイレクトされる
- ✅ 「決済が完了しました」等の成功メッセージが表示される
- ✅ 契約情報のサマリーが表示される
- ✅ 「ダッシュボードへ」リンクが表示される
- ✅ 「決済履歴へ」リンクが表示される

**スクリーンショット**: `screenshots/test-004-step6.png`

---

### ステップ7: データベース確認（tbl_company）

MySQLクライアントまたはphpMyAdminで以下のクエリを実行：

```sql
SELECT
    company_id,
    company_name,
    stripe_customer_id,
    stripe_subscription_id,
    subscription_status,
    subscription_plan,
    subscription_start_date,
    payment_date
FROM tbl_company
WHERE company_id = 1;  -- テストに使用した事業所ID
```

**期待結果**:
- ✅ `stripe_customer_id` が `cus_` で始まる値で埋まっている
- ✅ `stripe_subscription_id` が `sub_` で始まる値で埋まっている
- ✅ `subscription_status` が `active` になっている
- ✅ `subscription_plan` にプラン名が入っている（例: スタンダードプラン）
- ✅ `subscription_start_date` が現在日時付近になっている
- ✅ `payment_date` が現在日時から約1ヶ月後（有効期限）になっている

**実行結果をスクリーンショット**: `screenshots/test-004-step7.png`

---

### ステップ8: データベース確認（tbl_payment_history）

```sql
SELECT
    id,
    company_id,
    stripe_customer_id,
    stripe_subscription_id,
    amount,
    currency,
    status,
    plan_name,
    payment_date,
    webhook_event_id
FROM tbl_payment_history
WHERE company_id = 1
ORDER BY payment_date DESC
LIMIT 1;
```

**期待結果**:
- ✅ 新しいレコードが1件作成されている
- ✅ `company_id` が正しい
- ✅ `stripe_customer_id` が `cus_` で始まる
- ✅ `stripe_subscription_id` が `sub_` で始まる
- ✅ `amount` がプランの金額と一致（例: 10000.00）
- ✅ `currency` が `jpy`
- ✅ `status` が `succeeded`
- ✅ `plan_name` にプラン名が入っている
- ✅ `payment_date` が現在日時付近

**実行結果をスクリーンショット**: `screenshots/test-004-step8.png`

---

### ステップ9: データベース確認（tbl_stripe_webhooks）

```sql
SELECT
    id,
    event_id,
    event_type,
    processed,
    received_at,
    processed_at
FROM tbl_stripe_webhooks
WHERE event_type = 'checkout.session.completed'
ORDER BY received_at DESC
LIMIT 1;
```

**期待結果**:
- ✅ 新しいWebhookイベントが記録されている
- ✅ `event_id` が `evt_` で始まる
- ✅ `event_type` が `checkout.session.completed`
- ✅ `processed` が `1`（処理済み）
- ✅ `received_at` と `processed_at` が記録されている

**注意**: ローカル環境ではCSRF保護の問題でWebhookが動作しない場合があります。その場合、このステップはスキップし、本番環境で確認してください。

**実行結果をスクリーンショット**: `screenshots/test-004-step9.png`

---

### ステップ10: 決済履歴ページの確認

1. ブラウザで `/company/payment-history` にアクセス
   - URL: `http://localhost/DayCare.app/welfare/company/payment-history`

**期待結果**:
- ✅ 決済履歴ページが表示される
- ✅ 先ほど実行した決済が一覧の最上部に表示される
- ✅ 決済日、プラン名、金額、ステータス（成功）が正しく表示される
- ✅ ステータスが「成功」（緑色のラベル）で表示される

**スクリーンショット**: `screenshots/test-004-step10.png`

---

### ステップ11: Stripe Dashboardでの確認

1. Stripe Dashboard（https://dashboard.stripe.com/test/payments）にアクセス
2. テストモードであることを確認
3. 「支払い」タブをクリック

**期待結果**:
- ✅ 最新の決済が一覧に表示される
- ✅ 金額が正しい
- ✅ ステータスが「成功」
- ✅ 顧客メールアドレスが表示される

4. 「顧客」タブをクリック

**期待結果**:
- ✅ 新しい顧客が作成されている
- ✅ 顧客IDが `cus_` で始まる
- ✅ メタデータに `company_id` が記録されている

5. 「サブスクリプション」タブをクリック

**期待結果**:
- ✅ 新しいサブスクリプションが作成されている
- ✅ ステータスが「有効」
- ✅ プラン名が正しい
- ✅ 次回請求日が約1ヶ月後

**スクリーンショット**: `screenshots/test-004-step11-payments.png`, `step11-customers.png`, `step11-subscriptions.png`

---

## 5. テスト結果記録

### 5.1 テスト実施情報

| 項目 | 内容 |
|------|------|
| テスト実施日 | YYYY-MM-DD |
| テスト実施者 | [名前] |
| 実施環境 | ローカル開発環境 (XAMPP) |
| ブラウザ | Google Chrome / Firefox / Edge |
| 所要時間 | XX分 |

### 5.2 テスト結果サマリー

| ステップ | 結果 | 備考 |
|---------|------|------|
| 1. ログイン | ✅ PASS / ❌ FAIL |  |
| 2. 料金プランページ表示 | ✅ PASS / ❌ FAIL |  |
| 3. プラン選択 | ✅ PASS / ❌ FAIL |  |
| 4. 決済情報入力 | ✅ PASS / ❌ FAIL |  |
| 5. 決済実行 | ✅ PASS / ❌ FAIL |  |
| 6. 決済成功ページ表示 | ✅ PASS / ❌ FAIL |  |
| 7. DB確認（tbl_company） | ✅ PASS / ❌ FAIL |  |
| 8. DB確認（tbl_payment_history） | ✅ PASS / ❌ FAIL |  |
| 9. DB確認（tbl_stripe_webhooks） | ✅ PASS / ❌ FAIL |  |
| 10. 決済履歴ページ表示 | ✅ PASS / ❌ FAIL |  |
| 11. Stripe Dashboard確認 | ✅ PASS / ❌ FAIL |  |

### 5.3 総合判定

- ⭕ **合格**: 全ステップがPASS
- ❌ **不合格**: 1つでもFAILがある場合

**判定**: ⭕ 合格 / ❌ 不合格

---

## 6. 不具合報告

テスト中に発見した不具合を記録してください。

### 不具合1

- **発生ステップ**:
- **現象**:
- **期待動作**:
- **再現手順**:
- **スクリーンショット**:
- **優先度**: High / Medium / Low
- **状態**: Open / Fixed / Won't Fix

---

## 7. 備考・所感

テスト実施時の気づき、改善提案などを記載：

-
-
-

---

## 8. 関連ドキュメント

- [要件定義書](../requirements/stripe-payment-requirements.md)
- [技術仕様書](../specifications/stripe-payment-specification.md)
- [タスク管理表](../tasks/stripe-payment-tasks.md)
- TEST-005: 決済フロー統合テスト（失敗ケース）
- TEST-006: Webhook処理テスト

---

**テスト手順書作成日**: 2025-10-03
**作成者**: Claude (AI Assistant)
**バージョン**: 1.0
