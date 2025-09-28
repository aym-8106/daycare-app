<?php
// 緊急キャッシュクリア用スクリプト

echo "<h2>キャッシュクリア実行中...</h2>";

try {
    // 設定ファイルを読み込み
    require_once __DIR__ . '/../vendor/autoload.php';

    // Laravelアプリケーション起動
    $app = require_once __DIR__ . '/../bootstrap/app.php';

    // キャッシュクリア実行
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

    echo "<p>✅ config:clear 実行中...</p>";
    $kernel->call('config:clear');

    echo "<p>✅ cache:clear 実行中...</p>";
    $kernel->call('cache:clear');

    echo "<p>✅ view:clear 実行中...</p>";
    $kernel->call('view:clear');

    echo "<h3 style='color: green;'>✅ すべてのキャッシュクリアが完了しました！</h3>";
    echo "<p><a href='https://carenavi.site/'>サイトを確認する</a></p>";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ エラーが発生しました:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";

    // 手動でキャッシュディレクトリをクリア
    echo "<h3>手動キャッシュクリアを実行中...</h3>";

    $cacheDir = __DIR__ . '/../storage/framework/cache/data';
    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<p>✅ キャッシュファイルを削除しました</p>";
    }

    $viewDir = __DIR__ . '/../storage/framework/views';
    if (is_dir($viewDir)) {
        $files = glob($viewDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        echo "<p>✅ ビューキャッシュを削除しました</p>";
    }
}
?>