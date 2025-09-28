<?php
// 緊急用 index.php - public_html フォルダに配置用

// エラー表示を有効化
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 緊急診断ツール</h2>";

// 基本情報表示
echo "<h3>1. 基本情報</h3>";
echo "<p>現在のディレクトリ: " . getcwd() . "</p>";
echo "<p>このファイルの場所: " . __FILE__ . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";

// Laravelのautoloader確認
echo "<h3>2. Laravel確認</h3>";
$autoloaderPath = __DIR__ . '/../vendor/autoload.php';
echo "<p>Autoloader Path: $autoloaderPath</p>";

if (file_exists($autoloaderPath)) {
    echo "<p>✅ vendor/autoload.php 存在</p>";

    try {
        require_once $autoloaderPath;
        echo "<p>✅ Autoloader 読み込み成功</p>";

        $appPath = __DIR__ . '/../bootstrap/app.php';
        echo "<p>App Path: $appPath</p>";

        if (file_exists($appPath)) {
            echo "<p>✅ bootstrap/app.php 存在</p>";

            try {
                $app = require_once $appPath;
                echo "<p>✅ Laravel Application 作成成功</p>";

                // 簡単なリクエスト処理
                $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
                $request = Illuminate\Http\Request::capture();
                $response = $kernel->handle($request);

                echo "<h3>3. Laravel 実行結果</h3>";
                echo "<div style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc;'>";
                echo $response->getContent();
                echo "</div>";

                $kernel->terminate($request, $response);

            } catch (Exception $e) {
                echo "<p>❌ Laravel Application エラー: " . $e->getMessage() . "</p>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";

                // 緊急用HTML出力
                showEmergencyDashboard();
            }
        } else {
            echo "<p>❌ bootstrap/app.php が見つかりません</p>";
            showEmergencyDashboard();
        }

    } catch (Exception $e) {
        echo "<p>❌ Autoloader エラー: " . $e->getMessage() . "</p>";
        showEmergencyDashboard();
    }
} else {
    echo "<p>❌ vendor/autoload.php が見つかりません</p>";
    showEmergencyDashboard();
}

function showEmergencyDashboard() {
    $currentTime = date('Y年m月d日 H:i');

    echo "
    <hr>
    <h2>🏥 緊急ダッシュボード</h2>
    <div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 20px auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
        <div style='background: #007cba; color: white; padding: 20px; margin: -20px -20px 20px; border-radius: 8px 8px 0 0;'>
            <h1>🏥 通所介護管理システム</h1>
            <p>緊急モード | $currentTime</p>
        </div>

        <div style='background: #fff3cd; color: #856404; padding: 15px; border: 1px solid #ffeaa7; border-radius: 4px; margin: 20px 0;'>
            <h3>⚠️ 緊急モードで動作中</h3>
            <p>Laravel の完全な起動に問題があります。緊急用の簡易ダッシュボードを表示しています。</p>
        </div>

        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;'>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>システム</div>
                <div>緊急モード</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>PHP</div>
                <div>" . PHP_VERSION . "</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>サーバー</div>
                <div>エックスサーバー</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>稼働</div>
                <div>部分的</div>
            </div>
        </div>

        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;'>
            <div style='background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; border: 1px solid #dee2e6;'>
                <h3 style='margin: 0 0 10px; color: #007cba;'>🔧 システム診断</h3>
                <p style='margin: 10px 0; color: #666;'>現在のシステム状態を確認</p>
                <button onclick=\"location.reload()\" style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>再診断</button>
            </div>

            <div style='background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; border: 1px solid #dee2e6;'>
                <h3 style='margin: 0 0 10px; color: #007cba;'>📁 ファイル確認</h3>
                <p style='margin: 10px 0; color: #666;'>重要ファイルの存在確認</p>
                <button onclick=\"alert('緊急モードでは利用できません')\" style='background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>確認実行</button>
            </div>

            <div style='background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; border: 1px solid #dee2e6;'>
                <h3 style='margin: 0 0 10px; color: #007cba;'>🏥 介護管理</h3>
                <p style='margin: 10px 0; color: #666;'>基本的な管理機能</p>
                <button onclick=\"alert('正常モードに復旧後、利用可能になります')\" style='background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>管理画面</button>
            </div>
        </div>

        <hr style='margin: 40px 0;'>
        <div style='text-align: center; color: #666;'>
            <p><strong>通所介護管理システム v1.0 - 緊急モード</strong></p>
            <p>問題解決のため、開発者にお問い合わせください</p>
        </div>
    </div>
    ";
}
?>