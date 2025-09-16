# 🏥 CareNavi - 介護現場向け勤怠管理システム

介護施設向けの包括的な勤怠管理Webアプリケーションです。WordPress + CodeIgniterのハイブリッド構成で、効率的な職員管理と勤怠追跡を実現します。

## 🎯 システム概要

### アーキテクチャ
- **フロントエンド**: WordPress (メインサイト)
- **管理システム**: CodeIgniter 3.x (welfare ディレクトリ)
- **データベース**: MySQL 5.7+
- **UI**: Bootstrap 5 + レスポンシブデザイン

## 🚀 主要機能

### 📱 職員向け機能
- **モバイル打刻システム**
  - GPS位置情報記録
  - 写真撮影機能
  - QRコード読み取り打刻
  - リアルタイム時刻表示

- **勤怠管理**
  - 出勤・退勤打刻
  - 休憩時間管理
  - 残業申請・記録
  - 勤怠修正申請

### 👨‍💼 管理者機能
- **リアルタイムダッシュボード**
  - 職員勤怠状況の即座確認
  - 遅刻・欠勤・残業者の把握
  - 異常勤怠の自動検出

- **レポート・分析**
  - 月次勤怠集計
  - 個人別詳細レポート
  - CSV/Excel エクスポート
  - 統計データ可視化

- **職員管理**
  - 職員マスタ管理
  - 権限・役割設定
  - 勤務パターン管理
  - 一括インポート/エクスポート

## 📊 技術仕様

### システム要件
- **サーバー**: PHP 7.4+ / Apache 2.4+
- **データベース**: MySQL 5.7+
- **ブラウザ**: Chrome 80+, Safari 13+, Firefox 75+

### セキュリティ機能
- GPS位置情報による勤務地確認
- 打刻時の写真認証
- QRコード認証システム
- 役割ベースアクセス制御
- データ暗号化

## 🏗️ ディレクトリ構造

```
public_html/
├── wp-*                    # WordPress コアファイル
├── wp-content/             # WordPress コンテンツ
├── welfare/                # 勤怠管理システム (CodeIgniter)
│   ├── application/
│   │   ├── controllers/
│   │   │   ├── Attendance.php
│   │   │   ├── AttendanceReport.php
│   │   │   ├── AdminDashboard.php
│   │   │   └── StaffManagement.php
│   │   ├── models/
│   │   │   └── Attendance_model.php
│   │   ├── views/
│   │   │   └── attendance/
│   │   │       └── mobile_punch.php
│   │   ├── config/
│   │   │   └── attendance_config.php
│   │   └── db/
│   │       └── attendance_enhancement.sql
│   └── uploads/
│       └── attendance_photos/
└── uploads/                # アップロードファイル
```

## 💾 データベース設計

### 主要テーブル
- `tbl_attendance` - 勤怠データ
- `tbl_staff` - 職員マスタ
- `tbl_work_pattern` - 勤務パターン
- `tbl_leave_request` - 休暇申請
- `tbl_attendance_correction` - 勤怠修正申請

## 📱 モバイル対応

- **PWA対応** - アプリライクな操作体験
- **オフライン機能** - 一時的な通信断対応
- **カメラ連携** - 写真撮影・QRコード読み取り
- **GPS連携** - 位置情報自動取得
- **レスポンシブUI** - 全デバイス対応

## 🔧 セットアップ

### 1. 環境準備
```bash
# データベース作成
mysql -u root -p
CREATE DATABASE careNavi_db;

# ファイル権限設定
chmod 755 welfare/uploads
chmod 755 wp-content/uploads
```

### 2. データベース初期化
```sql
-- 拡張テーブルの作成
source welfare/application/db/attendance_enhancement.sql;
```

### 3. 設定ファイル編集
- `wp-config.php` - WordPress設定
- `welfare/application/config/database.php` - DB接続設定
- `welfare/application/config/attendance_config.php` - システム設定

## 🔐 セキュリティ考慮事項

⚠️ **重要なセキュリティファイル**
- `wp-config.php` - WordPressのDB認証情報
- `welfare/application/config/database.php` - CodeIgniterのDB設定
- `db.php` - Adminerファイル（本番では削除推奨）

### セキュリティ強化推奨事項
1. Adminerファイルの削除または保護
2. .htaccessでの適切なアクセス制御
3. SSL証明書の設定
4. 定期的なパスワード変更
5. バックアップの自動化

## 📈 今後の拡張計画

- [ ] AI異常勤怠検知
- [ ] 音声打刻機能
- [ ] 多言語対応
- [ ] 顔認証システム
- [ ] 給与計算連携
- [ ] モバイルアプリ版

## 🤝 コントリビューション

プロジェクトへの貢献を歓迎します：

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 ライセンス

このプロジェクトはMITライセンスの下で公開されています。

## 📞 サポート

- **ドキュメント**: `welfare/README_ATTENDANCE_SYSTEM.md`
- **Issues**: GitHub Issues でバグ報告・機能要望
- **Wiki**: 詳細な設定・運用ガイド

---

**開発**: Claude Code Assistant
**バージョン**: 1.0.0
**最終更新**: 2024年9月16日

🎉 効率的な介護現場の勤怠管理を実現しましょう！