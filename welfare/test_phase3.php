<?php
/**
 * Phase 3 動作確認テストスクリプト
 *
 * フロントエンド（ビューファイル）の確認
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "==============================================\n";
echo "Phase 3 動作確認テスト\n";
echo "==============================================\n\n";

$test_results = [];
$all_tests_passed = true;

// ============================================
// テスト1: ビューファイル存在確認
// ============================================
echo "【テスト1】ビューファイル存在確認\n";
echo "---------------------------------------------\n";

$views = [
    'payment' => __DIR__ . '/application/views/company/payment.php',
    'payment_success' => __DIR__ . '/application/views/company/payment_success.php',
    'payment_cancel' => __DIR__ . '/application/views/company/payment_cancel.php',
    'payment_history' => __DIR__ . '/application/views/company/payment_history.php',
];

$views_ok = true;
foreach ($views as $name => $path) {
    if (file_exists($path)) {
        $size = filesize($path);
        echo "✅ PASS: {$name}.php が存在します\n";
        echo "   パス: {$path}\n";
        echo "   サイズ: " . number_format($size) . " bytes\n";
        $test_results["view_{$name}"] = true;
    } else {
        echo "❌ FAIL: {$name}.php が見つかりません\n";
        echo "   パス: {$path}\n";
        $test_results["view_{$name}"] = false;
        $views_ok = false;
        $all_tests_passed = false;
    }
    echo "\n";
}

// ============================================
// テスト2: ビューファイルの内容チェック
// ============================================
echo "【テスト2】ビューファイル内容チェック\n";
echo "---------------------------------------------\n";

// payment.php のチェック
echo "payment.php:\n";
if (file_exists($views['payment'])) {
    $content = file_get_contents($views['payment']);

    $checks = [
        'stripe-pricing-table' => 'Stripe Pricing Table要素',
        'stripe_pricing_table_id' => 'Pricing Table ID変数',
        'stripe_publishable_key' => '公開可能キー変数',
    ];

    $payment_ok = true;
    foreach ($checks as $needle => $description) {
        if (strpos($content, $needle) !== false) {
            echo "  ✅ {$description}\n";
        } else {
            echo "  ❌ {$description} (見つかりません)\n";
            $payment_ok = false;
            $all_tests_passed = false;
        }
    }
    $test_results['payment_content'] = $payment_ok;
} else {
    echo "  ⚠️  ファイルが存在しないためスキップ\n";
    $test_results['payment_content'] = false;
}

echo "\n";

// payment_success.php のチェック
echo "payment_success.php:\n";
if (file_exists($views['payment_success'])) {
    $content = file_get_contents($views['payment_success']);

    if (strpos($content, '決済が完了') !== false || strpos($content, '決済完了') !== false) {
        echo "  ✅ 決済完了メッセージ\n";
        $test_results['success_content'] = true;
    } else {
        echo "  ❌ 決済完了メッセージ (見つかりません)\n";
        $test_results['success_content'] = false;
        $all_tests_passed = false;
    }
} else {
    echo "  ⚠️  ファイルが存在しないためスキップ\n";
    $test_results['success_content'] = false;
}

echo "\n";

// payment_cancel.php のチェック
echo "payment_cancel.php:\n";
if (file_exists($views['payment_cancel'])) {
    $content = file_get_contents($views['payment_cancel']);

    if (strpos($content, 'キャンセル') !== false) {
        echo "  ✅ キャンセルメッセージ\n";
        $test_results['cancel_content'] = true;
    } else {
        echo "  ❌ キャンセルメッセージ (見つかりません)\n";
        $test_results['cancel_content'] = false;
        $all_tests_passed = false;
    }
} else {
    echo "  ⚠️  ファイルが存在しないためスキップ\n";
    $test_results['cancel_content'] = false;
}

echo "\n";

// payment_history.php のチェック
echo "payment_history.php:\n";
if (file_exists($views['payment_history'])) {
    $content = file_get_contents($views['payment_history']);

    $checks = [
        'table' => 'テーブル要素',
        '$payments' => '決済データ変数',
        'payment_date' => '決済日カラム',
    ];

    $history_ok = true;
    foreach ($checks as $needle => $description) {
        if (strpos($content, $needle) !== false) {
            echo "  ✅ {$description}\n";
        } else {
            echo "  ❌ {$description} (見つかりません)\n";
            $history_ok = false;
            $all_tests_passed = false;
        }
    }
    $test_results['history_content'] = $history_ok;
} else {
    echo "  ⚠️  ファイルが存在しないためスキップ\n";
    $test_results['history_content'] = false;
}

echo "\n";

// ============================================
// テスト3: PHP構文チェック
// ============================================
echo "【テスト3】PHP構文チェック\n";
echo "---------------------------------------------\n";

$syntax_ok = true;
foreach ($views as $name => $path) {
    if (file_exists($path)) {
        $output = [];
        $return_var = 0;
        exec("php -l \"{$path}\" 2>&1", $output, $return_var);

        if ($return_var === 0) {
            echo "✅ PASS: {$name}.php の構文OK\n";
        } else {
            echo "❌ FAIL: {$name}.php に構文エラー\n";
            echo "   " . implode("\n   ", $output) . "\n";
            $syntax_ok = false;
            $all_tests_passed = false;
        }
    }
}
$test_results['syntax'] = $syntax_ok;

echo "\n";

// ============================================
// テスト4: 統合確認
// ============================================
echo "【テスト4】Phase 1-3 統合確認\n";
echo "---------------------------------------------\n";

// Phase 1
$phase1_files = [
    'Stripe_lib' => __DIR__ . '/application/libraries/Stripe_lib.php',
    'Payment_model' => __DIR__ . '/application/models/Payment_model.php',
    'Webhook_model' => __DIR__ . '/application/models/Webhook_model.php',
];

echo "Phase 1 (ライブラリ・モデル):\n";
$phase1_ok = true;
foreach ($phase1_files as $name => $path) {
    if (file_exists($path)) {
        echo "  ✅ {$name}\n";
    } else {
        echo "  ❌ {$name}\n";
        $phase1_ok = false;
    }
}

// Phase 2
$phase2_files = [
    'StripeWebhook' => __DIR__ . '/application/controllers/StripeWebhook.php',
    'Company' => __DIR__ . '/application/controllers/Company.php',
];

echo "\nPhase 2 (コントローラー):\n";
$phase2_ok = true;
foreach ($phase2_files as $name => $path) {
    if (file_exists($path)) {
        echo "  ✅ {$name}\n";
    } else {
        echo "  ❌ {$name}\n";
        $phase2_ok = false;
    }
}

// Phase 3
echo "\nPhase 3 (ビュー):\n";
$phase3_ok = $views_ok;
foreach ($views as $name => $path) {
    if (file_exists($path)) {
        echo "  ✅ {$name}\n";
    } else {
        echo "  ❌ {$name}\n";
        $phase3_ok = false;
    }
}

$test_results['integration'] = $phase1_ok && $phase2_ok && $phase3_ok;

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
    echo "Phase 3 の実装は正常に完了しています。\n\n";

    echo "==============================================\n";
    echo "Stripe決済機能 実装完了！\n";
    echo "==============================================\n\n";

    echo "✅ Phase 1: 環境準備・設計 - 完了\n";
    echo "✅ Phase 2: バックエンド実装 - 完了\n";
    echo "✅ Phase 3: フロントエンド実装 - 完了\n\n";

    echo "==============================================\n";
    echo "次のステップ: 実際の動作確認\n";
    echo "==============================================\n\n";

    echo "1. XAMPPでApacheとMySQLを起動\n\n";

    echo "2. ブラウザでアクセス:\n";
    echo "   http://localhost/DayCare.app/welfare/\n\n";

    echo "3. 事業所アカウントでログイン\n\n";

    echo "4. 料金プランページにアクセス:\n";
    echo "   http://localhost/DayCare.app/welfare/company/payment\n\n";

    echo "5. テスト決済を実行:\n";
    echo "   - Stripeのテストカード番号を使用\n";
    echo "   - カード番号: 4242 4242 4242 4242\n";
    echo "   - 有効期限: 任意の未来の日付\n";
    echo "   - CVC: 任意の3桁\n\n";

    echo "6. Webhook テスト (オプション):\n";
    echo "   stripe listen --forward-to http://localhost/DayCare.app/welfare/stripe_webhook\n\n";

    echo "⚠️  注意事項:\n";
    echo "   - 共通ヘッダー/フッターが未設定の場合、レイアウトが崩れる可能性があります\n";
    echo "   - その場合は、既存のビューファイルと同様にincludeを追加してください\n\n";

    exit(0);
} else {
    echo "⚠️  一部のテストが失敗しました。\n";
    echo "上記のエラーメッセージを確認してください。\n\n";
    exit(1);
}
