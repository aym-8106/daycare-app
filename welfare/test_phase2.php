<?php
/**
 * Phase 2 動作確認テストスクリプト
 *
 * このファイルは開発環境でのみ使用してください
 * 本番環境では削除してください
 */

// エラー表示設定
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "==============================================\n";
echo "Phase 2 動作確認テスト\n";
echo "==============================================\n\n";

$test_results = [];
$all_tests_passed = true;

// ============================================
// テスト1: コントローラーファイル確認
// ============================================
echo "【テスト1】コントローラーファイル確認\n";
echo "---------------------------------------------\n";

$controllers = [
    'StripeWebhook' => __DIR__ . '/application/controllers/StripeWebhook.php',
    'Company' => __DIR__ . '/application/controllers/Company.php',
];

foreach ($controllers as $name => $path) {
    if (file_exists($path)) {
        echo "✅ PASS: {$name}.php が存在します\n";
        echo "   パス: {$path}\n";

        // ファイルサイズを表示
        $size = filesize($path);
        echo "   サイズ: " . number_format($size) . " bytes\n";

        // 構文チェック
        $output = [];
        $return_var = 0;
        exec("php -l \"{$path}\"", $output, $return_var);

        if ($return_var === 0) {
            echo "   ✅ 構文チェックOK\n";
            $test_results["controller_{$name}"] = true;
        } else {
            echo "   ❌ 構文エラー\n";
            echo "   " . implode("\n   ", $output) . "\n";
            $test_results["controller_{$name}"] = false;
            $all_tests_passed = false;
        }
    } else {
        echo "❌ FAIL: {$name}.php が見つかりません\n";
        echo "   パス: {$path}\n";
        $test_results["controller_{$name}"] = false;
        $all_tests_passed = false;
    }
    echo "\n";
}

// ============================================
// テスト2: CSRF除外設定確認
// ============================================
echo "【テスト2】CSRF除外設定確認\n";
echo "---------------------------------------------\n";

$config_file = __DIR__ . '/application/config/config.php';
if (file_exists($config_file)) {
    $config_content = file_get_contents($config_file);

    if (strpos($config_content, 'stripe_webhook') !== false) {
        echo "✅ PASS: CSRF除外設定に stripe_webhook が含まれています\n";
        $test_results['csrf_exclude'] = true;
    } else {
        echo "❌ FAIL: CSRF除外設定に stripe_webhook が含まれていません\n";
        $test_results['csrf_exclude'] = false;
        $all_tests_passed = false;
    }
} else {
    echo "❌ FAIL: config.php が見つかりません\n";
    $test_results['csrf_exclude'] = false;
    $all_tests_passed = false;
}

echo "\n";

// ============================================
// テスト3: コントローラーの必須メソッド確認
// ============================================
echo "【テスト3】コントローラーの必須メソッド確認\n";
echo "---------------------------------------------\n";

// StripeWebhook.php のメソッド確認
echo "StripeWebhook.php のメソッド:\n";
$webhook_file = file_get_contents(__DIR__ . '/application/controllers/StripeWebhook.php');
$required_methods = [
    'index',
    'handleCheckoutSessionCompleted',
    'handleSubscriptionCreated',
    'handleSubscriptionUpdated',
    'handleSubscriptionDeleted',
    'handleInvoicePaymentSucceeded',
    'handleInvoicePaymentFailed',
];

$webhook_methods_ok = true;
foreach ($required_methods as $method) {
    if (preg_match('/function\s+' . $method . '\s*\(/', $webhook_file)) {
        echo "  ✅ {$method}()\n";
    } else {
        echo "  ❌ {$method}() (見つかりません)\n";
        $webhook_methods_ok = false;
        $all_tests_passed = false;
    }
}
$test_results['webhook_methods'] = $webhook_methods_ok;

echo "\n";

// Company.php のメソッド確認
echo "Company.php のメソッド:\n";
$company_file = file_get_contents(__DIR__ . '/application/controllers/Company.php');
$required_company_methods = [
    'payment',
    'create_checkout_session',
    'payment_success',
    'payment_cancel',
    'payment_history',
    'subscription',
    'cancel_subscription',
];

$company_methods_ok = true;
foreach ($required_company_methods as $method) {
    if (preg_match('/function\s+' . $method . '\s*\(/', $company_file)) {
        echo "  ✅ {$method}()\n";
    } else {
        echo "  ❌ {$method}() (見つかりません)\n";
        $company_methods_ok = false;
        $all_tests_passed = false;
    }
}
$test_results['company_methods'] = $company_methods_ok;

echo "\n";

// ============================================
// テスト4: 依存関係確認
// ============================================
echo "【テスト4】Phase 1 との依存関係確認\n";
echo "---------------------------------------------\n";

$dependencies = [
    'Stripe_lib' => __DIR__ . '/application/libraries/Stripe_lib.php',
    'Payment_model' => __DIR__ . '/application/models/Payment_model.php',
    'Webhook_model' => __DIR__ . '/application/models/Webhook_model.php',
    'Company_model' => __DIR__ . '/application/models/Company_model.php',
];

$dependencies_ok = true;
foreach ($dependencies as $name => $path) {
    if (file_exists($path)) {
        echo "✅ PASS: {$name} が存在します\n";
    } else {
        echo "❌ FAIL: {$name} が見つかりません\n";
        echo "   パス: {$path}\n";
        $dependencies_ok = false;
        $all_tests_passed = false;
    }
}
$test_results['dependencies'] = $dependencies_ok;

echo "\n";

// ============================================
// テスト5: コントローラー内のセキュリティチェック
// ============================================
echo "【テスト5】セキュリティ設定確認\n";
echo "---------------------------------------------\n";

// StripeWebhook でのCSRF保護無効化コメント確認
if (strpos($webhook_file, 'CSRF') !== false) {
    echo "✅ PASS: StripeWebhook.php にCSRF関連のコメントがあります\n";
    $test_results['webhook_csrf_comment'] = true;
} else {
    echo "⚠️  WARN: StripeWebhook.php にCSRF関連のコメントがありません\n";
    $test_results['webhook_csrf_comment'] = false;
}

// Company コントローラーでの認証チェック確認
if (strpos($company_file, 'session->userdata') !== false) {
    echo "✅ PASS: Company.php に認証チェックがあります\n";
    $test_results['company_auth'] = true;
} else {
    echo "❌ FAIL: Company.php に認証チェックがありません\n";
    $test_results['company_auth'] = false;
    $all_tests_passed = false;
}

// Stripe署名検証の確認
if (strpos($webhook_file, 'constructWebhookEvent') !== false) {
    echo "✅ PASS: StripeWebhook.php にWebhook署名検証があります\n";
    $test_results['webhook_signature'] = true;
} else {
    echo "❌ FAIL: StripeWebhook.php にWebhook署名検証がありません\n";
    $test_results['webhook_signature'] = false;
    $all_tests_passed = false;
}

echo "\n";

// ============================================
// テスト6: エンドポイントパス確認
// ============================================
echo "【テスト6】エンドポイント情報\n";
echo "---------------------------------------------\n";

echo "実装されたエンドポイント:\n";
echo "  📍 POST   /company/create_checkout_session\n";
echo "  📍 GET    /company/payment\n";
echo "  📍 GET    /company/payment-success\n";
echo "  📍 GET    /company/payment-cancel\n";
echo "  📍 GET    /company/payment-history\n";
echo "  📍 GET    /company/subscription\n";
echo "  📍 POST   /company/cancel_subscription\n";
echo "  📍 POST   /stripe_webhook/index\n";

echo "\n⚠️  注意: これらのエンドポイントにアクセスするには:\n";
echo "  1. XAMPPでApacheを起動\n";
echo "  2. ブラウザでアクセス: http://localhost/DayCare.app/welfare/\n";
echo "  3. ログインが必要（/company/* エンドポイント）\n";

$test_results['endpoints'] = true;

echo "\n";

// ============================================
// テスト結果サマリー
// ============================================
echo "==============================================\n";
echo "テスト結果サマリー\n";
echo "==============================================\n";

$passed = 0;
$failed = 0;

foreach ($test_results as $test => $result) {
    if ($result) {
        $passed++;
    } else {
        $failed++;
    }
}

$total = $passed + $failed;
$pass_rate = $total > 0 ? round(($passed / $total) * 100, 1) : 0;

echo "総テスト数: {$total}\n";
echo "成功: {$passed}\n";
echo "失敗: {$failed}\n";
echo "成功率: {$pass_rate}%\n\n";

if ($all_tests_passed) {
    echo "🎉 すべてのテストに合格しました！\n";
    echo "Phase 2 の実装は正常に完了しています。\n\n";

    echo "==============================================\n";
    echo "次のステップ: Phase 2 の手動テスト\n";
    echo "==============================================\n";
    echo "以下の手順で動作確認を行ってください:\n\n";

    echo "1. XAMPPでApacheとMySQLを起動\n\n";

    echo "2. ブラウザで以下のURLにアクセス:\n";
    echo "   http://localhost/DayCare.app/welfare/\n\n";

    echo "3. 事業所アカウントでログイン\n\n";

    echo "4. 料金プランページにアクセス:\n";
    echo "   http://localhost/DayCare.app/welfare/company/payment\n\n";

    echo "5. Stripe CLIでWebhookテスト（オプション）:\n";
    echo "   stripe listen --forward-to http://localhost/DayCare.app/welfare/stripe_webhook\n\n";

    echo "⚠️  Phase 3（フロントエンド）が未実装のため、\n";
    echo "   ビューファイルが見つからないエラーが出ます。\n";
    echo "   これは正常です。Phase 3 で解決します。\n\n";

    exit(0);
} else {
    echo "⚠️  一部のテストが失敗しました。\n";
    echo "上記のエラーメッセージを確認してください。\n\n";
    exit(1);
}
