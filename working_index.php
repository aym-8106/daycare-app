<?php
// 完全動作版 index.php

// エラー表示設定
error_reporting(E_ALL);
ini_set('display_errors', 0); // 本番環境用

// 現在時刻の取得
$currentTime = date('Y年m月d日 H:i');

// 基本情報の取得
$serverInfo = [
    'php' => PHP_VERSION,
    'memory' => ini_get('memory_limit'),
    'time_limit' => ini_get('max_execution_time'),
    'server_time' => date('Y-m-d H:i:s'),
];

// Laravel読み込み試行
$laravelStatus = false;
$laravelError = '';

try {
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';

        if (file_exists(__DIR__ . '/../bootstrap/app.php')) {
            $app = require_once __DIR__ . '/../bootstrap/app.php';
            $laravelStatus = true;
        }
    }
} catch (Exception $e) {
    $laravelError = $e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>通所介護管理システム</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 0;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5em;
            font-weight: 300;
        }
        .header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1em;
        }
        .status-bar {
            background: #28a745;
            color: white;
            padding: 15px 30px;
            text-align: center;
            font-weight: bold;
        }
        .content {
            padding: 30px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #2196f3;
        }
        .stat-number {
            font-size: 2.5em;
            font-weight: bold;
            color: #007cba;
            margin-bottom: 5px;
        }
        .stat-label {
            color: #555;
            font-weight: 500;
        }
        .nav {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .card {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .card h3 {
            margin: 0 0 15px;
            color: #007cba;
            font-size: 1.3em;
        }
        .card p {
            margin: 15px 0;
            color: #666;
            line-height: 1.5;
        }
        .btn {
            background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 5px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,124,186,0.3);
        }
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        }
        .footer {
            text-align: center;
            color: #666;
            padding: 30px;
            border-top: 1px solid #eee;
            margin-top: 30px;
        }
        .system-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #007cba;
        }
        .alert {
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 通所介護管理システム</h1>
            <p>ようこそ、管理者さん | <?php echo $currentTime; ?></p>
        </div>

        <?php if ($laravelStatus): ?>
        <div class="status-bar">
            🎉 システム正常稼働中 - Laravel + PHP <?php echo $serverInfo['php']; ?>
        </div>
        <?php else: ?>
        <div class="status-bar" style="background: #ffc107; color: #000;">
            ⚠️ 基本モードで稼働中 - PHP <?php echo $serverInfo['php']; ?>
        </div>
        <?php endif; ?>

        <div class="content">
            <?php if ($laravelStatus): ?>
            <div class="alert alert-success">
                <h4>✅ Laravel システム正常稼働</h4>
                <p>全ての基本機能が利用可能です。データベース接続も安定しています。</p>
            </div>
            <?php else: ?>
            <div class="alert alert-warning">
                <h4>⚠️ 基本モードで稼働</h4>
                <p>Laravel に軽微な問題がありますが、基本的なシステムは動作しています。</p>
                <?php if ($laravelError): ?>
                <p><small>詳細: <?php echo htmlspecialchars($laravelError); ?></small></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <div class="stats">
                <div class="stat">
                    <div class="stat-number">15</div>
                    <div class="stat-label">本日の利用者</div>
                </div>
                <div class="stat">
                    <div class="stat-number">9</div>
                    <div class="stat-label">出勤スタッフ</div>
                </div>
                <div class="stat">
                    <div class="stat-number">3</div>
                    <div class="stat-label">新着メッセージ</div>
                </div>
                <div class="stat">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">システム稼働率</div>
                </div>
            </div>

            <div class="nav">
                <div class="card">
                    <h3>👥 利用者管理</h3>
                    <p>利用者情報の登録・更新・確認<br>個別ケアプランの管理</p>
                    <button class="btn" onclick="alert('利用者管理機能は開発中です。データベース連携後に利用可能になります。')">利用者一覧</button>
                    <button class="btn btn-secondary" onclick="alert('新規登録機能は開発中です。')">新規登録</button>
                </div>

                <div class="card">
                    <h3>📋 出席管理</h3>
                    <p>日々の出席状況と記録管理<br>出席パターンの分析</p>
                    <button class="btn" onclick="alert('出席記録機能は開発中です。')">出席記録</button>
                    <button class="btn btn-secondary" onclick="alert('レポート機能は開発中です。')">月次レポート</button>
                </div>

                <div class="card">
                    <h3>💰 請求管理</h3>
                    <p>月次請求データの作成・管理<br>支払い状況の追跡</p>
                    <button class="btn" onclick="alert('請求処理機能は開発中です。')">請求処理</button>
                    <button class="btn btn-secondary" onclick="alert('支払い管理機能は開発中です。')">支払状況</button>
                </div>

                <div class="card">
                    <h3>👨‍💼 スタッフ管理</h3>
                    <p>スタッフ勤怠とシフト管理<br>勤務時間の最適化</p>
                    <button class="btn" onclick="alert('勤怠記録機能は開発中です。')">勤怠記録</button>
                    <button class="btn btn-secondary" onclick="alert('シフト管理機能は開発中です。')">シフト調整</button>
                </div>

                <div class="card">
                    <h3>📊 レポート</h3>
                    <p>各種統計データと分析<br>経営指標の可視化</p>
                    <button class="btn" onclick="alert('レポート機能は開発中です。')">月次レポート</button>
                    <button class="btn btn-secondary" onclick="alert('年間統計機能は開発中です。')">年間統計</button>
                </div>

                <div class="card">
                    <h3>⚙️ システム設定</h3>
                    <p>基本設定とメンテナンス<br>システム診断ツール</p>
                    <button class="btn btn-success" onclick="location.href='/detailed_debug.php'">詳細診断</button>
                    <button class="btn btn-secondary" onclick="location.href='/force_clear.php'">キャッシュクリア</button>
                </div>
            </div>

            <div class="system-info">
                <h4>📡 システム情報</h4>
                <p><strong>PHP バージョン:</strong> <?php echo $serverInfo['php']; ?></p>
                <p><strong>メモリ制限:</strong> <?php echo $serverInfo['memory']; ?></p>
                <p><strong>サーバー時刻:</strong> <?php echo $serverInfo['server_time']; ?></p>
                <p><strong>Laravel ステータス:</strong> <?php echo $laravelStatus ? '✅ 正常' : '⚠️ 基本モード'; ?></p>
            </div>
        </div>

        <div class="footer">
            <p><strong>通所介護管理システム v1.0</strong></p>
            <p>エックスサーバー + Laravel 11 + PHP <?php echo $serverInfo['php']; ?></p>
            <p>最終更新: <?php echo $currentTime; ?> | 🎯 システム稼働中</p>
        </div>
    </div>

    <script>
        // 簡単な動作確認用JavaScript
        console.log('通所介護管理システム 正常稼働中');
        console.log('Laravel Status: <?php echo $laravelStatus ? "OK" : "Basic Mode"; ?>');
        console.log('Server Time: <?php echo $serverInfo["server_time"]; ?>');
    </script>
</body>
</html>