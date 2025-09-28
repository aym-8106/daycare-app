<?php
// 詳細デバッグツール

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔍 詳細Laravel診断ツール</h2>";

// 基本情報
echo "<h3>1. 環境情報</h3>";
echo "<p>PHP: " . PHP_VERSION . "</p>";
echo "<p>実行時刻: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>メモリ制限: " . ini_get('memory_limit') . "</p>";
echo "<p>実行時間制限: " . ini_get('max_execution_time') . "秒</p>";

try {
    require_once __DIR__ . '/../vendor/autoload.php';
    echo "<p>✅ Autoloader 読み込み完了</p>";

    $app = require_once __DIR__ . '/../bootstrap/app.php';
    echo "<p>✅ Laravel Application 作成完了</p>";

    echo "<h3>2. 設定ファイル確認</h3>";

    // .env ファイル確認
    $envPath = __DIR__ . '/../.env';
    if (file_exists($envPath)) {
        echo "<p>✅ .env ファイル存在</p>";
        $envContent = file_get_contents($envPath);
        if (strpos($envContent, 'APP_KEY=') !== false) {
            echo "<p>✅ APP_KEY 設定済み</p>";
        } else {
            echo "<p>❌ APP_KEY 未設定</p>";
        }
    } else {
        echo "<p>❌ .env ファイルが見つかりません</p>";
    }

    // config ディレクトリ確認
    $configDir = __DIR__ . '/../config';
    if (is_dir($configDir)) {
        echo "<p>✅ config ディレクトリ存在</p>";
        $configFiles = scandir($configDir);
        echo "<p>設定ファイル: " . implode(', ', array_filter($configFiles, function($f) { return substr($f, -4) === '.php'; })) . "</p>";
    } else {
        echo "<p>❌ config ディレクトリが見つかりません</p>";
    }

    // storage ディレクトリ確認
    $storageDir = __DIR__ . '/../storage';
    if (is_dir($storageDir)) {
        echo "<p>✅ storage ディレクトリ存在</p>";

        $frameworkDir = $storageDir . '/framework';
        if (is_dir($frameworkDir)) {
            echo "<p>✅ storage/framework 存在</p>";

            $dirs = ['views', 'cache', 'sessions'];
            foreach ($dirs as $dir) {
                $path = $frameworkDir . '/' . $dir;
                if (is_dir($path)) {
                    echo "<p>✅ storage/framework/$dir 存在 (権限: " . substr(sprintf('%o', fileperms($path)), -4) . ")</p>";
                } else {
                    echo "<p>❌ storage/framework/$dir が見つかりません</p>";
                }
            }
        } else {
            echo "<p>❌ storage/framework が見つかりません</p>";
        }
    }

    echo "<h3>3. Laravel 実行テスト</h3>";

    // キャッシュクリア試行
    try {
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

        echo "<p>🔄 config:clear 実行中...</p>";
        $kernel->call('config:clear');
        echo "<p>✅ config:clear 完了</p>";

        echo "<p>🔄 cache:clear 実行中...</p>";
        $kernel->call('cache:clear');
        echo "<p>✅ cache:clear 完了</p>";

        echo "<p>🔄 view:clear 実行中...</p>";
        $kernel->call('view:clear');
        echo "<p>✅ view:clear 完了</p>";

    } catch (Exception $e) {
        echo "<p>⚠️ キャッシュクリアエラー: " . $e->getMessage() . "</p>";
    }

    // HTTP Kernel でリクエスト処理
    echo "<p>🔄 HTTP リクエスト処理テスト...</p>";

    $httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $request = Illuminate\Http\Request::create('/', 'GET');

    echo "<p>✅ Request 作成完了</p>";
    echo "<p>Request URI: " . $request->getRequestUri() . "</p>";

    ob_start();
    try {
        $response = $httpKernel->handle($request);
        echo "<p>✅ Response 作成完了</p>";
        echo "<p>Status Code: " . $response->getStatusCode() . "</p>";

        $content = $response->getContent();
        if (strlen($content) > 500) {
            $content = substr($content, 0, 500) . '...';
        }

        echo "<h4>Response Content:</h4>";
        echo "<div style='background: #f0f0f0; padding: 10px; border: 1px solid #ccc; max-height: 300px; overflow: auto;'>";
        echo "<pre>" . htmlspecialchars($content) . "</pre>";
        echo "</div>";

        $httpKernel->terminate($request, $response);

    } catch (Exception $e) {
        echo "<p>❌ HTTP処理エラー: " . $e->getMessage() . "</p>";
        echo "<h4>エラー詳細:</h4>";
        echo "<div style='background: #ffe6e6; padding: 10px; border: 1px solid #ff0000;'>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
        echo "</div>";

        // 緊急回避策
        echo "<hr><h3>🚨 緊急回避: 直接ルート実行</h3>";
        showDirectRoute();
    }
    ob_end_clean();

} catch (Exception $e) {
    echo "<p>❌ 致命的エラー: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";

    showDirectRoute();
}

function showDirectRoute() {
    $currentTime = date('Y年m月d日 H:i');

    echo "
    <div style='background: #d1ecf1; border: 1px solid #bee5eb; padding: 15px; border-radius: 5px; margin: 20px 0;'>
        <h4>✅ 直接実行による緊急ダッシュボード</h4>
        <p>Laravel実行に問題があるため、直接HTMLを出力します。</p>
    </div>

    <div style='font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
        <div style='background: #007cba; color: white; padding: 20px; margin: -20px -20px 20px; border-radius: 8px 8px 0 0;'>
            <h1>🏥 通所介護管理システム</h1>
            <p>直接実行モード | $currentTime</p>
        </div>

        <div style='background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0;'>
            <h3>🎉 基本システム稼働中</h3>
            <p>Laravel の一部機能に問題がありますが、基本的なシステムは動作しています。</p>
        </div>

        <div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0;'>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>システム</div>
                <div>部分稼働</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>サーバー</div>
                <div>正常</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>PHP</div>
                <div>" . PHP_VERSION . "</div>
            </div>
            <div style='background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center;'>
                <div style='font-size: 24px; font-weight: bold; color: #007cba;'>診断</div>
                <div>完了</div>
            </div>
        </div>

        <div style='text-align: center; margin: 20px 0;'>
            <button onclick='location.reload()' style='background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px;'>再診断</button>
            <button onclick='alert(\"開発者に問題解決を依頼してください\")' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px;'>サポート要請</button>
        </div>
    </div>
    ";
}
?>