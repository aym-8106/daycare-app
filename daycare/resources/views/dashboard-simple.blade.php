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
        .alert {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border: 1px solid #c3e6cb;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            color: #666;
            padding: 30px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🏥 通所介護管理システム</h1>
            <p>ようこそ、{{ $userName }}さん | {{ $currentTime }}</p>
        </div>

        <div class="status-bar">
            🎉 Laravel Blade テンプレート正常稼働中
        </div>

        <div class="content">
            <div class="alert">
                <h4>✅ 標準Laravel アプリケーション復旧成功</h4>
                <p>本来のLaravelアプリケーションとして正常に動作しています。</p>
            </div>

            <div class="stats">
                <div class="stat">
                    <div class="stat-number">{{ $stats['users'] }}</div>
                    <div>本日の利用者</div>
                </div>
                <div class="stat">
                    <div class="stat-number">{{ $stats['staff'] }}</div>
                    <div>出勤スタッフ</div>
                </div>
                <div class="stat">
                    <div class="stat-number">{{ $stats['messages'] }}</div>
                    <div>新着メッセージ</div>
                </div>
                <div class="stat">
                    <div class="stat-number">{{ $stats['uptime'] }}</div>
                    <div>システム稼働率</div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p><strong>通所介護管理システム v1.0</strong></p>
            <p>Laravel {{ app()->version() }} + PHP {{ PHP_VERSION }}</p>
            <p>最終更新: {{ $currentTime }}</p>
        </div>
    </div>
</body>
</html>