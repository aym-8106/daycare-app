<?php
// 強制キャッシュクリア（データベース非依存）

echo "<h2>🧹 強制キャッシュクリア</h2>";

try {
    // 手動でキャッシュファイルを削除
    $cacheDir = __DIR__ . '/storage/framework/cache/data';
    $viewDir = __DIR__ . '/storage/framework/views';

    echo "<p>🔄 キャッシュディレクトリクリア中...</p>";

    if (is_dir($cacheDir)) {
        $files = glob($cacheDir . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p>✅ キャッシュファイル {$count} 件削除</p>";
    }

    if (is_dir($viewDir)) {
        $files = glob($viewDir . '/*');
        $count = 0;
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $count++;
            }
        }
        echo "<p>✅ ビューキャッシュファイル {$count} 件削除</p>";
    }

    echo "<p>✅ 強制キャッシュクリア完了</p>";
    echo "<p><a href='/'>サイトを確認</a></p>";

} catch (Exception $e) {
    echo "<p>❌ エラー: " . $e->getMessage() . "</p>";
}
?>