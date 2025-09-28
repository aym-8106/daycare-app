#!/bin/bash

# 通所介護管理システム - Xサーバーデプロイスクリプト

echo "=== 通所介護管理システム デプロイ開始 ==="

# 環境確認
echo "1. 環境確認中..."
php -v
if [ $? -ne 0 ]; then
    echo "エラー: PHPが見つかりません"
    exit 1
fi

# .envファイルの確認
if [ ! -f .env ]; then
    echo "2. .envファイルをコピー..."
    cp .env.example .env
    echo ".envファイルを編集してください"
    exit 1
fi

# アプリケーションキー生成
echo "3. アプリケーションキー生成..."
php artisan key:generate --force

# キャッシュクリア
echo "4. キャッシュクリア..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# データベースマイグレーション
echo "5. データベースマイグレーション実行..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    echo "エラー: マイグレーションに失敗しました"
    exit 1
fi

# 初期データ投入
echo "6. 初期データ投入..."
php artisan db:seed --force

# ストレージリンク作成
echo "7. ストレージリンク作成..."
php artisan storage:link

# ファイル権限設定
echo "8. ファイル権限設定..."
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
mkdir -p storage/logs
chmod 755 storage/logs

# 設定キャッシュ
echo "9. 設定キャッシュ生成..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=== デプロイ完了 ==="
echo ""
echo "次の手順を実行してください："
echo "1. ブラウザでアプリケーションにアクセス"
echo "2. 初期ユーザーでログイン確認"
echo "3. データベース接続確認"
echo "4. 本番用パスワードに変更"
echo ""
echo "初期ログイン情報："
echo "管理者: yamada@sakura.example.com / password123"
echo "職員: sato@sakura.example.com / password123"