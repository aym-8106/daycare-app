@echo off
echo =====================================
echo 通所介護管理システム - ローカル環境セットアップ
echo =====================================
echo.

echo 1. Composerの依存関係をインストール中...
call composer install
if %errorlevel% neq 0 (
    echo エラー: Composerのインストールに失敗しました
    echo Composerがインストールされているか確認してください
    pause
    exit /b 1
)

echo.
echo 2. 環境設定ファイルをコピー中...
if not exist .env (
    copy .env.example .env
    echo .envファイルを作成しました
) else (
    echo .envファイルは既に存在します
)

echo.
echo 3. アプリケーションキーを生成中...
call php artisan key:generate
if %errorlevel% neq 0 (
    echo エラー: キー生成に失敗しました
    pause
    exit /b 1
)

echo.
echo 4. データベースの作成を確認してください
echo XAMPPのphpMyAdminで以下のデータベースを作成：
echo データベース名: daycare_local
echo 文字コード: utf8mb4_unicode_ci
echo.
echo データベースを作成しましたか？ (Y/N)
set /p confirm=
if /i "%confirm%" neq "Y" (
    echo データベースを作成してから再度実行してください
    pause
    exit /b 1
)

echo.
echo 5. データベースマイグレーション実行中...
call php artisan migrate
if %errorlevel% neq 0 (
    echo エラー: マイグレーションに失敗しました
    echo データベース設定を確認してください
    pause
    exit /b 1
)

echo.
echo 6. 初期データを投入中...
call php artisan db:seed
if %errorlevel% neq 0 (
    echo エラー: シード実行に失敗しました
    pause
    exit /b 1
)

echo.
echo 7. ストレージリンクを作成中...
call php artisan storage:link

echo.
echo =====================================
echo セットアップ完了！
echo =====================================
echo.
echo 次の手順：
echo 1. XAMPPでApache・MySQLを起動
echo 2. http://localhost/DayCare.app/daycare/public にアクセス
echo.
echo 初期ログイン情報:
echo 管理者: yamada@sakura.example.com / password123
echo 職員: sato@sakura.example.com / password123
echo.
pause