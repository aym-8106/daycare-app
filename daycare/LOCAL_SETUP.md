# ローカル環境セットアップガイド

## 📋 必要な環境

- Windows 10/11
- XAMPP (Apache + MySQL + PHP)
- Composer
- 任意: Node.js

## 🚀 セットアップ手順

### ステップ 1: XAMPPのインストールと起動

1. **XAMPPをダウンロード**
   - https://www.apachefriends.org/jp/index.html
   - 「XAMPP for Windows」をダウンロード

2. **XAMPPをインストール**
   - ダウンロードしたファイルを実行
   - デフォルト設定でインストール（`C:\xampp`）

3. **XAMPPを起動**
   - `C:\xampp\xampp-control.exe` を管理者として実行
   - **Apache** と **MySQL** の「Start」ボタンをクリック
   - 緑色になったら起動成功

### ステップ 2: Composerのインストール

1. **Composerをダウンロード**
   - https://getcomposer.org/download/
   - 「Composer-Setup.exe」をダウンロード

2. **Composerをインストール**
   - ダウンロードしたファイルを実行
   - PHPのパスは自動検出される（`C:\xampp\php\php.exe`）
   - デフォルト設定でインストール

### ステップ 3: データベース作成

1. **phpMyAdminにアクセス**
   - ブラウザで http://localhost/phpmyadmin を開く

2. **データベースを作成**
   - 左側の「新規作成」をクリック
   - データベース名: `daycare_local`
   - 照合順序: `utf8mb4_unicode_ci`
   - 「作成」ボタンをクリック

### ステップ 4: アプリケーションのセットアップ

1. **コマンドプロンプトを開く**
   - `Win + R` → `cmd` → Enter
   - または「コマンドプロンプト」で検索

2. **プロジェクトディレクトリに移動**
   ```cmd
   cd C:\xampp\htdocs\DayCare.app\daycare
   ```

3. **自動セットアップを実行**
   ```cmd
   setup-local.bat
   ```

   このバッチファイルが以下を自動実行します：
   - Composerの依存関係インストール
   - .envファイルの作成
   - アプリケーションキー生成
   - データベースマイグレーション
   - 初期データ投入

### ステップ 5: アプリケーションにアクセス

1. **ブラウザを開く**
   - http://localhost/DayCare.app/daycare/public にアクセス

2. **ログイン**

   **さくらデイサービス（管理者）**
   - メールアドレス: `yamada@sakura.example.com`
   - パスワード: `password123`

   **さくらデイサービス（職員）**
   - メールアドレス: `sato@sakura.example.com`
   - パスワード: `password123`

## 🔧 手動セットアップ（バッチファイルが動かない場合）

```cmd
# 1. 依存関係インストール
composer install

# 2. 環境ファイルコピー
copy .env.local .env

# 3. アプリケーションキー生成
php artisan key:generate

# 4. データベースマイグレーション
php artisan migrate

# 5. 初期データ投入
php artisan db:seed

# 6. ストレージリンク作成
php artisan storage:link
```

## ❗ トラブルシューティング

### 問題1: "Composer command not found"
**解決方法:**
- コマンドプロンプトを再起動
- Composerを再インストール
- 環境変数PATHにComposerが追加されているか確認

### 問題2: データベース接続エラー
**解決方法:**
1. XAMPPでMySQLが起動しているか確認
2. phpMyAdminで`daycare_local`データベースが作成されているか確認
3. `.env`ファイルの設定確認：
   ```
   DB_HOST=127.0.0.1
   DB_DATABASE=daycare_local
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 問題3: 404 Not Found エラー
**解決方法:**
- URLが正しいか確認: `http://localhost/DayCare.app/daycare/public`
- XAMPPでApacheが起動しているか確認
- ファイルが正しい場所にあるか確認

### 問題4: "Class not found" エラー
**解決方法:**
```cmd
composer dump-autoload
php artisan config:clear
php artisan route:clear
```

## 📁 ディレクトリ構成

```
C:\xampp\htdocs\DayCare.app\
└── daycare\
    ├── app\              # アプリケーション本体
    ├── public\           # Webサーバーのドキュメントルート
    ├── database\         # マイグレーション・シーダー
    ├── resources\views\  # Bladeテンプレート
    ├── .env             # 環境設定（自動作成）
    └── setup-local.bat  # セットアップスクリプト
```

## 🎯 次のステップ

1. **機能確認**
   - 勤怠打刻
   - シフト作成
   - 日次スケジュール
   - メッセージ投稿

2. **開発環境整備**
   - Visual Studio Code等のエディタ
   - Gitでのバージョン管理
   - デバッグ環境

困ったことがあれば、エラーメッセージと一緒にお聞かせください！