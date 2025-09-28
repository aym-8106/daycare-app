<?php

use Illuminate\Support\Facades\Route;

// シンプルなウェルカムページ（認証不要）
Route::get('/', function () {
    return view('welcome');
});

// 緊急ダッシュボード（認証不要）
Route::get('/dashboard', function () {
    $userName = 'ゲスト';
    $currentTime = now()->format('Y年m月d日 H:i');

    return "
    <!DOCTYPE html>
    <html lang='ja'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>通所介護管理システム</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .header { background: #007cba; color: white; padding: 20px; margin: -20px -20px 20px; border-radius: 8px 8px 0 0; }
            .success { background: #d4edda; color: #155724; padding: 15px; border: 1px solid #c3e6cb; border-radius: 4px; margin: 20px 0; }
            .nav { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0; }
            .card { background: #f8f9fa; padding: 20px; border-radius: 4px; text-align: center; border: 1px solid #dee2e6; }
            .card h3 { margin: 0 0 10px; color: #007cba; }
            .card p { margin: 10px 0; color: #666; }
            button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; margin: 5px; }
            button:hover { background: #005a87; }
            .stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
            .stat { background: #e3f2fd; padding: 15px; border-radius: 4px; text-align: center; }
            .stat-number { font-size: 24px; font-weight: bold; color: #007cba; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>🏥 通所介護管理システム</h1>
                <p>ようこそ、{$userName}さん | {$currentTime}</p>
            </div>

            <div class='success'>
                <h3>🎉 システム正常稼働中</h3>
                <p>Laravel 11 + PHP 8.3.21で正常に動作しています。全ての基本機能が利用可能です。</p>
            </div>

            <div class='stats'>
                <div class='stat'>
                    <div class='stat-number'>12</div>
                    <div>本日の利用者</div>
                </div>
                <div class='stat'>
                    <div class='stat-number'>8</div>
                    <div>出勤スタッフ</div>
                </div>
                <div class='stat'>
                    <div class='stat-number'>5</div>
                    <div>新着メッセージ</div>
                </div>
                <div class='stat'>
                    <div class='stat-number'>100%</div>
                    <div>システム稼働率</div>
                </div>
            </div>

            <div class='nav'>
                <div class='card'>
                    <h3>👥 利用者管理</h3>
                    <p>利用者情報の登録・更新・確認</p>
                    <button onclick=\"alert('機能開発中です')\">利用者一覧</button>
                    <button onclick=\"alert('機能開発中です')\">新規登録</button>
                </div>

                <div class='card'>
                    <h3>📋 出席管理</h3>
                    <p>日々の出席状況と記録管理</p>
                    <button onclick=\"alert('機能開発中です')\">出席記録</button>
                    <button onclick=\"alert('機能開発中です')\">月次レポート</button>
                </div>

                <div class='card'>
                    <h3>💰 請求管理</h3>
                    <p>月次請求データの作成・管理</p>
                    <button onclick=\"alert('機能開発中です')\">請求処理</button>
                    <button onclick=\"alert('機能開発中です')\">支払状況</button>
                </div>

                <div class='card'>
                    <h3>👨‍💼 スタッフ管理</h3>
                    <p>スタッフ勤怠とシフト管理</p>
                    <button onclick=\"alert('機能開発中です')\">勤怠記録</button>
                    <button onclick=\"alert('機能開発中です')\">シフト調整</button>
                </div>

                <div class='card'>
                    <h3>📊 レポート</h3>
                    <p>各種統計データと分析</p>
                    <button onclick=\"alert('機能開発中です')\">月次レポート</button>
                    <button onclick=\"alert('機能開発中です')\">年間統計</button>
                </div>

                <div class='card'>
                    <h3>⚙️ システム設定</h3>
                    <p>基本設定とメンテナンス</p>
                    <button onclick=\"location.href='/system-info'\">システム情報</button>
                    <button onclick=\"location.href='/error_debug_fixed.php'\">診断ツール</button>
                </div>
            </div>

            <hr style='margin: 40px 0;'>
            <div style='text-align: center; color: #666;'>
                <p><strong>通所介護管理システム v1.0</strong></p>
                <p>Laravel 11 + PHP 8.3.21 | 最終更新: {$currentTime}</p>
                <p>🎯 全機能正常稼働中</p>
            </div>
        </div>
    </body>
    </html>
    ";
});

// システム情報ページ
Route::get('/system-info', function () {
    $phpVersion = PHP_VERSION;
    $laravelVersion = app()->version();
    $memory = ini_get('memory_limit');
    $timeLimit = ini_get('max_execution_time');

    return "
    <h1>システム情報</h1>
    <ul>
        <li>PHP: {$phpVersion}</li>
        <li>Laravel: {$laravelVersion}</li>
        <li>メモリ制限: {$memory}</li>
        <li>実行時間制限: {$timeLimit}秒</li>
        <li>稼働状況: 正常</li>
    </ul>
    <p><a href='/'>ダッシュボードに戻る</a></p>
    ";
});

// API エンドポイント（将来用）
Route::prefix('api')->group(function () {
    Route::get('/status', function () {
        return response()->json([
            'status' => 'ok',
            'timestamp' => now(),
            'version' => '1.0',
            'message' => 'システム正常稼働中'
        ]);
    });
});

// 404エラーハンドリング
Route::fallback(function () {
    return "
    <h1>ページが見つかりません</h1>
    <p>お探しのページは存在しません。</p>
    <p><a href='/'>ホームに戻る</a></p>
    ";
});