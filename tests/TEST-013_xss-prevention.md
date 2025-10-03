# TEST-013: XSS対策テスト

**テストID**: TEST-013
**テスト種別**: セキュリティテスト（XSS - Cross-Site Scripting）
**所要時間**: 約20分
**前提条件**: Phase 3のフロントエンド実装が完了していること

---

## 1. テスト目的

決済履歴や契約情報の表示において、悪意のあるスクリプトが実行されないことを確認する。特に、ユーザー入力やStripeから取得したデータが適切にエスケープされていることを検証する。

---

## 2. XSS攻撃とは

### 2.1 XSS攻撃の種類

- **Reflected XSS**: URLパラメータ等からの即座の反映
- **Stored XSS**: データベースに保存された悪意あるスクリプト
- **DOM-based XSS**: JavaScriptによるDOM操作での脆弱性

### 2.2 対策方法

- ✅ `htmlspecialchars()` による HTML エスケープ
- ✅ CSP（Content Security Policy）ヘッダー
- ✅ 入力バリデーション
- ✅ 出力時の適切なエンコーディング

---

## 3. テスト環境

- **URL**: http://localhost/DayCare.app/welfare/
- **データベース**: daycare_welfare
- **テストツール**: ブラウザ開発者ツール、SQLクライアント

---

## 4. テスト手順

### テストケース1: 決済履歴のプラン名にXSSペイロード

#### ステップ1: 悪意のあるデータをデータベースに挿入

```sql
-- XSSペイロードを含む決済履歴を挿入
INSERT INTO tbl_payment_history (
    company_id,
    stripe_customer_id,
    amount,
    currency,
    status,
    plan_name,
    payment_date
) VALUES (
    1,
    'cus_xss_test',
    10000.00,
    'jpy',
    'succeeded',
    '<script>alert("XSS Attack!")</script>悪意のあるプラン',
    NOW()
);
```

#### ステップ2: 決済履歴ページを表示

1. ブラウザで `/company/payment-history` にアクセス
2. 開発者ツールのコンソールを開く

**期待結果**:
- ✅ JavaScriptアラートが**表示されない**
- ✅ `<script>` タグがそのままテキストとして表示される
- ✅ コンソールにエラーがない
- ✅ ページが正常に表示される

**実際の表示**:
```
<script>alert("XSS Attack!")</script>悪意のあるプラン
```
（スクリプトとして実行されず、テキストとして表示される）

**スクリーンショット**: `screenshots/test-013-case1-step2.png`

#### ステップ3: HTMLソースを確認

1. ページ上で右クリック → 「ページのソースを表示」
2. プラン名の部分を検索

**期待結果**:
- ✅ `&lt;script&gt;alert("XSS Attack!")&lt;/script&gt;` のようにエスケープされている
- ✅ 生の `<script>` タグは存在しない

**確認箇所**:
```html
<td>&lt;script&gt;alert("XSS Attack!")&lt;/script&gt;悪意のあるプラン</td>
```

#### ステップ4: テストデータを削除

```sql
-- テストデータを削除
DELETE FROM tbl_payment_history
WHERE stripe_customer_id = 'cus_xss_test';
```

---

### テストケース2: 請求書IDにXSSペイロード

#### ステップ1: XSSペイロードをstripe_invoice_idに挿入

```sql
INSERT INTO tbl_payment_history (
    company_id,
    stripe_customer_id,
    stripe_invoice_id,
    amount,
    currency,
    status,
    plan_name,
    payment_date
) VALUES (
    1,
    'cus_test2',
    'in_<img src=x onerror="alert(\'XSS\')" />',
    5000.00,
    'jpy',
    'succeeded',
    'テストプラン',
    NOW()
);
```

#### ステップ2: 決済履歴ページを表示

1. `/company/payment-history` にアクセス
2. 開発者ツールのコンソールを確認

**期待結果**:
- ✅ JavaScriptアラートが**表示されない**
- ✅ 画像が読み込まれない
- ✅ `onerror` イベントが実行されない
- ✅ HTMLエンティティとしてエスケープされている

**HTMLソース確認**:
```html
<td class="invoice-id">in_&lt;img src=x onerror="alert('XSS')" /&gt;</td>
```

#### ステップ3: テストデータを削除

```sql
DELETE FROM tbl_payment_history
WHERE stripe_customer_id = 'cus_test2';
```

---

### テストケース3: URLパラメータからのXSS（Reflected XSS）

#### ステップ1: エラーメッセージのXSS

1. ブラウザで以下のURLにアクセス:
```
http://localhost/DayCare.app/welfare/company/payment?error=<script>alert('XSS')</script>
```

2. 開発者ツールのコンソールを確認

**期待結果**:
- ✅ JavaScriptアラートが**表示されない**
- ✅ トースト通知にエスケープされたメッセージが表示される
- ✅ スクリプトが実行されない

**スクリーンショット**: `screenshots/test-013-case3-step1.png`

---

### テストケース4: サブスクリプションプラン名のXSS

#### ステップ1: XSSペイロードをsubscription_planに挿入

```sql
-- 事業所のプラン名にXSSペイロードを設定
UPDATE tbl_company
SET subscription_plan = '<svg onload="alert(\'XSS\')">'
WHERE company_id = 1;
```

#### ステップ2: 料金プランページを表示

1. `/company/payment` にアクセス
2. 「現在のご契約」セクションを確認

**期待結果**:
- ✅ SVGタグが実行されない
- ✅ onloadイベントが発火しない
- ✅ JavaScriptアラートが表示されない
- ✅ エスケープされたテキストとして表示される

**HTMLソース確認**:
```html
<div class="value">&lt;svg onload="alert('XSS')"&gt;</div>
```

#### ステップ3: 元に戻す

```sql
UPDATE tbl_company
SET subscription_plan = NULL
WHERE company_id = 1;
```

---

### テストケース5: ステータスラベルのXSS

#### ステップ1: statusカラムにXSSペイロード

```sql
INSERT INTO tbl_payment_history (
    company_id,
    amount,
    currency,
    status,
    plan_name,
    payment_date
) VALUES (
    1,
    1000.00,
    'jpy',
    'succeeded<img src=x onerror=alert(1)>',
    'テストプラン',
    NOW()
);
```

#### ステップ2: 決済履歴ページで確認

**期待結果**:
- ✅ ステータスラベルが正常に表示される
- ✅ XSSペイロードが実行されない
- ✅ 不正な値の場合は「default」ステータスとして表示される

#### ステップ3: クリーンアップ

```sql
DELETE FROM tbl_payment_history
WHERE status LIKE '%<img%';
```

---

### テストケース6: JavaScriptコード内のXSS

#### ステップ1: payment.phpのJavaScriptを確認

`payment.php` の JavaScript コード内で、サーバーサイド変数を使用している箇所を確認：

```php
// 悪い例（XSS脆弱性あり）
<script>
var planName = "<?php echo $plan_name; ?>";
</script>

// 良い例（エスケープ済み）
<script>
var planName = "<?php echo htmlspecialchars($plan_name, ENT_QUOTES, 'UTF-8'); ?>";
</script>
```

**確認結果**:
- ✅ PHP変数をJavaScript内で使用する際、`htmlspecialchars()` または `json_encode()` を使用している
- ✅ ユーザー入力がそのまま JavaScript コードに埋め込まれていない

---

## 5. コード確認

### 5.1 ビューファイルのエスケープチェック

以下のファイルで、全ての動的コンテンツが `htmlspecialchars()` または `<?= htmlspecialchars() ?>` でエスケープされていることを確認：

#### payment_history.php

```php
// ✅ 正しい例
<td><?php echo htmlspecialchars($payment['plan_name'] ?? '-'); ?></td>
<td class="invoice-id"><?php echo htmlspecialchars($payment['stripe_invoice_id'] ?? '-'); ?></td>

// ❌ 危険な例（使用していないことを確認）
<td><?php echo $payment['plan_name']; ?></td>
```

**確認結果**: ✅ 全てエスケープされている / ❌ 一部エスケープされていない

#### payment.php

```php
// ✅ 正しい例
<div class="value"><?php echo htmlspecialchars($subscription_plan ?? '未選択'); ?></div>

// ✅ Stripeパラメータも確認
<stripe-pricing-table
    pricing-table-id="<?php echo htmlspecialchars($stripe_pricing_table_id); ?>"
    publishable-key="<?php echo htmlspecialchars($stripe_publishable_key); ?>">
</stripe-pricing-table>
```

**確認結果**: ✅ 全てエスケープされている / ❌ 一部エスケープされていない

---

### 5.2 CSP（Content Security Policy）の確認

`.htaccess` または `header.php` で CSP が設定されていることを確認：

```apache
<IfModule mod_headers.c>
    Header set Content-Security-Policy "default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://js.stripe.com; ..."
</IfModule>
```

**確認結果**: ✅ CSP設定されている / ❌ 設定されていない

---

## 6. テスト結果記録

### 6.1 テスト実施情報

| 項目 | 内容 |
|------|------|
| テスト実施日 | YYYY-MM-DD |
| テスト実施者 | [名前] |
| 実施環境 | ローカル開発環境 |
| 所要時間 | XX分 |

### 6.2 テスト結果サマリー

| テストケース | 結果 | 備考 |
|------------|------|------|
| ケース1: プラン名のXSS | ✅ PASS / ❌ FAIL |  |
| ケース2: 請求書IDのXSS | ✅ PASS / ❌ FAIL |  |
| ケース3: URLパラメータのXSS | ✅ PASS / ❌ FAIL |  |
| ケース4: サブスクリプションプラン名のXSS | ✅ PASS / ❌ FAIL |  |
| ケース5: ステータスラベルのXSS | ✅ PASS / ❌ FAIL |  |
| ケース6: JavaScript内のXSS | ✅ PASS / ❌ FAIL |  |

### 6.3 コードレビュー

| チェック項目 | 結果 |
|-----------|------|
| payment_history.php のエスケープ | ✅ OK / ❌ NG |
| payment.php のエスケープ | ✅ OK / ❌ NG |
| CSP設定 | ✅ OK / ❌ NG |
| JavaScript内の変数使用 | ✅ OK / ❌ NG |

### 6.4 総合判定

**判定**: ⭕ 合格 / ❌ 不合格

---

## 7. 脆弱性スコアリング

### 7.1 発見された脆弱性

| 脆弱性ID | 深刻度 | 箇所 | 影響 | 対策状況 |
|---------|--------|-----|------|---------|
| - | - | - | - | - |

### 7.2 推奨事項

- ✅ 全ての動的コンテンツで `htmlspecialchars()` を使用
- ✅ CSPヘッダーを設定（`unsafe-inline` は最小限に）
- ✅ 入力バリデーションを強化
- ✅ セキュリティヘッダーの追加（X-XSS-Protection, X-Content-Type-Options等）

---

## 8. 追加のセキュリティ対策

### 8.1 推奨ヘッダー

```apache
# .htaccess に追加推奨
<IfModule mod_headers.c>
    Header set X-XSS-Protection "1; mode=block"
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

### 8.2 PHP設定

```php
// 出力時のデフォルトエンコーディング
ini_set('default_charset', 'UTF-8');
```

---

## 9. 参考資料

- [OWASP XSS Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/Cross_Site_Scripting_Prevention_Cheat_Sheet.html)
- [Content Security Policy (CSP)](https://developer.mozilla.org/en-US/docs/Web/HTTP/CSP)
- [PHP htmlspecialchars()](https://www.php.net/manual/en/function.htmlspecialchars.php)

---

**テスト手順書作成日**: 2025-10-03
**作成者**: Claude (AI Assistant)
**バージョン**: 1.0
