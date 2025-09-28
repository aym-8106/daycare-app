# 通所介護業務管理システム

Laravel 11ベースの通所介護（デイサービス）向け業務管理システムです。

## 主要機能

- **勤怠管理**: 打刻・月次集計・CSV出力・締めロック
- **シフト管理**: 月次シフト表・自動割当・手動編集
- **日次スケジュール**: 時間×職員の業務スケジュール管理
- **メッセージ機能**: 掲示板・ピン留め・既読管理・検索
- **RBAC**: 管理者・職員の権限管理
- **Stripe決済**: サブスクリプション管理（ひな形）

## 技術スタック

- **Backend**: Laravel 11
- **Frontend**: Blade + Bootstrap 5 + Alpine.js
- **Database**: MySQL 8.0+
- **Payment**: Stripe
- **Hosting**: Xサーバー対応

## Xサーバーでのデプロイ手順

### 1. 前提条件

- PHP 8.2以上
- MySQL 8.0以上
- Composer（ローカル開発時）

### 2. ファイルアップロード

1. FTPクライアントでXサーバーに接続
2. `daycare/` ディレクトリ全体を `/public_html/daycare/` にアップロード
3. DocumentRootを `/public_html/daycare/public/` に設定

### 3. データベース設定

```bash
# データベース作成（Xサーバーのコントロールパネルで実行）
# データベース名: yourdb_name
# ユーザー名: yourdb_user
# パスワード: yourdb_password
```

### 4. 環境設定

```bash
# .envファイルをコピー
cp .env.example .env

# .envファイルを編集
APP_NAME=通所介護管理システム
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com/daycare

DB_CONNECTION=mysql
DB_HOST=mysql123.xserver.jp
DB_DATABASE=yourdb_name
DB_USERNAME=yourdb_user
DB_PASSWORD=yourdb_password

STRIPE_KEY=pk_live_xxxxxxxxxx
STRIPE_SECRET=sk_live_xxxxxxxxxx
STRIPE_WEBHOOK_SECRET=whsec_xxxxxxxxxx
```

### 5. アプリケーション初期化

```bash
# アプリケーションキー生成
php artisan key:generate

# データベースマイグレーション実行
php artisan migrate --force

# 初期データ投入
php artisan db:seed

# キャッシュクリア
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### 6. ファイル権限設定

```bash
# ストレージディレクトリの権限設定
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# ログディレクトリ作成
mkdir -p storage/logs
chmod 755 storage/logs
```

### 7. .htaccess設定

`public/.htaccess` は既に設定済みです。

### 8. 初期ユーザー作成

初期データとして以下のユーザーが作成されます：

**さくらデイサービス**
- 管理者: yamada@sakura.example.com / password123
- 職員: sato@sakura.example.com / password123

**ひまわりデイサービス**
- 管理者: suzuki@himawari.example.com / password123
- 職員: takahashi@himawari.example.com / password123

## 運用時の注意事項

### セキュリティ

- 本番環境では必ず独自のパスワードに変更してください
- APP_DEBUGは必ずfalseに設定してください
- データベースの定期バックアップを設定してください

### バックアップ

```bash
# データベースバックアップ
mysqldump -u yourdb_user -p yourdb_name > backup_$(date +%Y%m%d).sql

# ファイルバックアップ
tar -czf daycare_backup_$(date +%Y%m%d).tar.gz daycare/
```

### ログモニタリング

- ログファイル: `storage/logs/laravel.log`
- エラー監視: Xサーバーのエラーログと併せて確認

## 開発・カスタマイズ

### ローカル開発環境

```bash
# 依存関係インストール
composer install
npm install

# 環境設定
cp .env.example .env
php artisan key:generate

# データベース設定
php artisan migrate
php artisan db:seed

# 開発サーバー起動
php artisan serve
```

### 主要ディレクトリ構成

```
daycare/
├── app/
│   ├── Http/Controllers/     # コントローラー
│   ├── Models/              # Eloquentモデル
│   ├── Policies/            # 認可ポリシー
│   └── Providers/           # サービスプロバイダー
├── database/
│   ├── migrations/          # データベースマイグレーション
│   └── seeders/            # シーダー
├── resources/
│   └── views/              # Bladeテンプレート
├── routes/
│   ├── web.php             # Webルート
│   └── api.php             # APIルート
└── public/                 # 公開ディレクトリ
```

### カスタマイズポイント

1. **業務ルール**: `app/Models/` 内のビジネスロジック
2. **画面レイアウト**: `resources/views/layouts/app.blade.php`
3. **権限設定**: `app/Policies/` 内のポリシー
4. **Stripe設定**: `app/Http/Controllers/Api/StripeWebhookController.php`

## トラブルシューティング

### よくある問題

1. **500エラー**: ログファイルとファイル権限を確認
2. **データベース接続エラー**: .envファイルの設定を確認
3. **ファイルアップロードエラー**: `php.ini`の`upload_max_filesize`を確認
4. **セッションエラー**: `storage/framework/sessions/`の権限を確認

### サポート

技術的な問題については、システム管理者までお問い合わせください。

## ライセンス

本システムは MIT License の下で提供されています。