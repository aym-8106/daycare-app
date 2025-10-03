<?php
/**
 * Phase 1 動作確認テストスクリプト
 *
 * このファイルは開発環境でのみ使用してください
 * 本番環境では削除してください
 */

// CodeIgniterのブートストラップ
define('BASEPATH', TRUE);
define('ENVIRONMENT', 'development');

// エラー表示設定
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "==============================================\n";
echo "Phase 1 動作確認テスト\n";
echo "==============================================\n\n";

$test_results = [];
$all_tests_passed = true;

// ============================================
// テスト1: stripe_config.php の読み込み確認
// ============================================
echo "【テスト1】stripe_config.php 読み込み確認\n";
echo "---------------------------------------------\n";

$config_file = __DIR__ . '/application/config/stripe_config.php';

if (!file_exists($config_file)) {
    echo "❌ FAIL: stripe_config.php が見つかりません\n";
    echo "   パス: {$config_file}\n\n";
    $test_results['config_file_exists'] = false;
    $all_tests_passed = false;
} else {
    echo "✅ PASS: stripe_config.php が存在します\n";
    $test_results['config_file_exists'] = true;

    // 設定ファイルを読み込み
    include $config_file;

    // 必須設定項目のチェック
    $required_configs = [
        'stripe_environment',
        'stripe_test_publishable_key',
        'stripe_test_secret_key',
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_currency',
        'stripe_pricing_table_id',
    ];

    $config_ok = true;
    foreach ($required_configs as $key) {
        if (!isset($config[$key])) {
            echo "❌ FAIL: 設定項目 '{$key}' が見つかりません\n";
            $config_ok = false;
            $all_tests_passed = false;
        } elseif (strpos($config[$key], 'xxxxx') !== false) {
            echo "⚠️  WARN: 設定項目 '{$key}' がダミー値のままです\n";
            echo "   値: {$config[$key]}\n";
        }
    }

    if ($config_ok) {
        echo "✅ PASS: すべての必須設定項目が存在します\n";
        $test_results['config_items'] = true;

        // 実際の値を表示
        echo "\n設定値の確認:\n";
        echo "  環境: {$config['stripe_environment']}\n";
        echo "  公開可能キー: " . substr($config['stripe_publishable_key'], 0, 20) . "...\n";
        echo "  シークレットキー: " . substr($config['stripe_secret_key'], 0, 20) . "...\n";
        echo "  通貨: {$config['stripe_currency']}\n";
        echo "  Pricing Table ID: {$config['stripe_pricing_table_id']}\n";
    } else {
        $test_results['config_items'] = false;
    }
}

echo "\n";

// ============================================
// テスト2: データベーステーブル確認
// ============================================
echo "【テスト2】データベーステーブル確認\n";
echo "---------------------------------------------\n";

// データベース接続情報を読み込み
$db_config_file = __DIR__ . '/application/config/database.php';
if (file_exists($db_config_file)) {
    include $db_config_file;

    $db_name = $db['default']['database'];
    $db_user = $db['default']['username'];
    $db_pass = $db['default']['password'];
    $db_host = $db['default']['hostname'];

    echo "データベース: {$db_name}\n";

    try {
        $pdo = new PDO("mysql:host={$db_host};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        echo "✅ PASS: データベース接続成功\n\n";
        $test_results['db_connection'] = true;

        // テーブル存在確認
        $tables = ['tbl_company', 'tbl_payment_history', 'tbl_stripe_webhooks'];

        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '{$table}'");
            if ($stmt->rowCount() > 0) {
                echo "✅ PASS: テーブル '{$table}' が存在します\n";

                // カラム数を確認
                $stmt = $pdo->query("DESCRIBE {$table}");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                echo "   カラム数: " . count($columns) . "\n";

                $test_results["table_{$table}"] = true;
            } else {
                echo "❌ FAIL: テーブル '{$table}' が見つかりません\n";
                $test_results["table_{$table}"] = false;
                $all_tests_passed = false;
            }
        }

        echo "\n";

        // tbl_company の Stripe関連カラム確認
        echo "tbl_company のStripe関連カラム確認:\n";
        $stripe_columns = [
            'stripe_customer_id',
            'stripe_subscription_id',
            'subscription_status',
            'subscription_plan',
            'payment_date',
            'subscription_start_date',
            'subscription_end_date'
        ];

        $stmt = $pdo->query("DESCRIBE tbl_company");
        $existing_columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $all_columns_exist = true;
        foreach ($stripe_columns as $col) {
            if (in_array($col, $existing_columns)) {
                echo "  ✅ {$col}\n";
            } else {
                echo "  ❌ {$col} (見つかりません)\n";
                $all_columns_exist = false;
                $all_tests_passed = false;
            }
        }

        $test_results['stripe_columns'] = $all_columns_exist;

        echo "\n";

        // インデックス確認
        echo "インデックス確認:\n";
        $stmt = $pdo->query("SHOW INDEX FROM tbl_company WHERE Key_name LIKE 'idx_%'");
        $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($indexes) > 0) {
            foreach ($indexes as $idx) {
                echo "  ✅ {$idx['Key_name']} ({$idx['Column_name']})\n";
            }
            $test_results['indexes'] = true;
        } else {
            echo "  ⚠️  インデックスが見つかりません\n";
            $test_results['indexes'] = false;
        }

    } catch (PDOException $e) {
        echo "❌ FAIL: データベース接続エラー\n";
        echo "   エラー: {$e->getMessage()}\n";
        $test_results['db_connection'] = false;
        $all_tests_passed = false;
    }
} else {
    echo "❌ FAIL: database.php が見つかりません\n";
    $test_results['db_config_exists'] = false;
    $all_tests_passed = false;
}

echo "\n";

// ============================================
// テスト3: ライブラリファイル確認
// ============================================
echo "【テスト3】ライブラリ・モデルファイル確認\n";
echo "---------------------------------------------\n";

$files_to_check = [
    'Stripe_lib' => __DIR__ . '/application/libraries/Stripe_lib.php',
    'Payment_model' => __DIR__ . '/application/models/Payment_model.php',
    'Webhook_model' => __DIR__ . '/application/models/Webhook_model.php',
];

foreach ($files_to_check as $name => $path) {
    if (file_exists($path)) {
        echo "✅ PASS: {$name} が存在します\n";
        echo "   パス: {$path}\n";

        // ファイルサイズを表示
        $size = filesize($path);
        echo "   サイズ: " . number_format($size) . " bytes\n";

        // 構文チェック（簡易）
        $content = file_get_contents($path);
        if (strpos($content, '<?php') === 0) {
            echo "   ✅ PHPファイル形式OK\n";
        }

        $test_results["file_{$name}"] = true;
    } else {
        echo "❌ FAIL: {$name} が見つかりません\n";
        echo "   パス: {$path}\n";
        $test_results["file_{$name}"] = false;
        $all_tests_passed = false;
    }
    echo "\n";
}

// ============================================
// テスト4: .gitignore 確認
// ============================================
echo "【テスト4】.gitignore 設定確認\n";
echo "---------------------------------------------\n";

$gitignore_file = dirname(__DIR__) . '/.gitignore';
if (file_exists($gitignore_file)) {
    $gitignore_content = file_get_contents($gitignore_file);

    if (strpos($gitignore_content, 'stripe_config.php') !== false) {
        echo "✅ PASS: .gitignore に stripe_config.php が含まれています\n";
        $test_results['gitignore'] = true;
    } else {
        echo "❌ FAIL: .gitignore に stripe_config.php が含まれていません\n";
        $test_results['gitignore'] = false;
        $all_tests_passed = false;
    }
} else {
    echo "⚠️  WARN: .gitignore が見つかりません\n";
    $test_results['gitignore'] = false;
}

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
    echo "Phase 1 の実装は正常に完了しています。\n\n";
    exit(0);
} else {
    echo "⚠️  一部のテストが失敗しました。\n";
    echo "上記のエラーメッセージを確認してください。\n\n";
    exit(1);
}
