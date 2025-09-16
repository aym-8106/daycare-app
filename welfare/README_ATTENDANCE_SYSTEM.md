# 介護現場向け勤怠管理システム

このシステムは介護現場での使用に特化した勤怠管理アプリケーションです。

## 🎯 主な機能

### 📱 職員向け機能
- **モバイル対応の打刻画面**
  - GPS位置情報記録
  - 写真撮影機能
  - QRコード読み取り打刻
  - リアルタイム時刻表示

- **勤怠データ管理**
  - 出勤・退勤打刻
  - 休憩時間記録
  - 残業時間管理
  - メモ・特記事項入力

- **申請機能**
  - 勤怠修正申請
  - 休暇申請
  - 残業申請

### 👨‍💼 管理者向け機能
- **リアルタイムダッシュボード**
  - 職員の勤怠状況一覧
  - 遅刻・欠勤者の確認
  - 残業者の把握
  - 異常勤怠の検出

- **勤怠レポート**
  - 月次勤怠集計
  - 個人別詳細レポート
  - CSV/Excel エクスポート
  - 統計データ表示

- **職員管理**
  - 職員マスタ管理
  - 権限設定
  - 勤務パターン設定
  - CSV インポート/エクスポート

### 🔧 システム管理機能
- **承認ワークフロー**
  - 修正申請の承認/却下
  - 自動承認設定
  - 通知機能

- **アラート機能**
  - 長時間労働の検出
  - 遅刻・欠勤の通知
  - カスタムアラート設定

## 📊 データベース構造

### 主要テーブル
1. **tbl_attendance** - 勤怠データ
2. **tbl_staff** - 職員マスタ
3. **tbl_work_pattern** - 勤務パターン
4. **tbl_leave_request** - 休暇申請
5. **tbl_attendance_correction** - 勤怠修正申請
6. **tbl_work_schedule** - 勤務予定

## 🚀 インストール・設定

### 1. データベース設定
```sql
-- attendance_enhancement.sql を実行してテーブルを作成
mysql -u username -p database_name < application/db/attendance_enhancement.sql
```

### 2. 設定ファイル
- `application/config/database.php` - DB接続設定
- `application/config/attendance_config.php` - システム設定

### 3. ディレクトリ権限
```bash
# アップロードディレクトリの作成・権限設定
mkdir uploads/attendance_photos
chmod 755 uploads/attendance_photos
```

## 📁 ファイル構造

```
welfare/
├── application/
│   ├── controllers/
│   │   ├── Attendance.php           # 勤怠打刻
│   │   ├── AttendanceReport.php     # 勤怠レポート
│   │   ├── AdminDashboard.php       # 管理ダッシュボード
│   │   └── StaffManagement.php      # 職員管理
│   ├── models/
│   │   └── Attendance_model.php     # 勤怠データモデル
│   ├── views/
│   │   └── attendance/
│   │       └── mobile_punch.php     # モバイル打刻画面
│   ├── config/
│   │   └── attendance_config.php    # システム設定
│   └── db/
│       └── attendance_enhancement.sql # DB定義
└── uploads/
    └── attendance_photos/           # 打刻時写真
```

## 🔐 セキュリティ機能

- **位置情報検証** - GPS による勤務場所確認
- **写真認証** - 打刻時の写真撮影
- **QRコード打刻** - 固有QRコードによる不正打刻防止
- **IPアドレス制限** - 特定のIPからのみアクセス許可
- **権限管理** - 役割に基づくアクセス制御

## 📱 モバイル対応

- **レスポンシブデザイン** - スマートフォン・タブレット対応
- **PWA対応** - アプリライクな操作感
- **オフライン機能** - 通信不良時の一時保存
- **カメラ連携** - 写真撮影・QRコード読み取り
- **GPS連携** - 位置情報の自動取得

## 📊 レポート機能

### エクスポート形式
- CSV形式
- Excel形式
- PDF形式（予定）

### レポート種別
- 日次勤怠レポート
- 月次勤怠集計
- 個人別勤怠詳細
- 異常勤怠レポート
- 残業時間集計

## 🔔 通知機能

- **メール通知**
  - 遅刻・欠勤アラート
  - 長時間労働通知
  - 修正申請通知

- **システム内通知**
  - ダッシュボードアラート
  - 承認待ち件数表示

## 🛠️ カスタマイズ

### 勤務パターンの追加
```php
// attendance_config.php
$config['work_patterns']['custom_shift'] = [
    'name' => 'カスタムシフト',
    'start_time' => '10:00:00',
    'end_time' => '19:00:00',
    'break_duration' => 60,
    'color' => '#27ae60'
];
```

### アラート設定のカスタマイズ
管理画面からアラート閾値を設定可能：
- 遅刻判定時間
- 残業判定時間
- 連続欠勤日数

## 📞 サポート

### トラブルシューティング

1. **打刻ができない場合**
   - ブラウザのキャッシュクリア
   - GPS設定の確認
   - インターネット接続確認

2. **写真が保存されない場合**
   - uploadディレクトリの権限確認
   - ファイルサイズ制限確認

3. **位置情報が取得できない場合**
   - ブラウザの位置情報許可確認
   - HTTPS環境での動作確認

### システム要件
- PHP 7.4以上
- MySQL 5.7以上
- CodeIgniter 3.x
- モダンブラウザ（Chrome, Safari, Firefox）

## 🔄 今後の拡張予定

- AI による異常勤怠検知
- 音声による打刻機能
- 多言語対応
- API連携機能の拡充
- 顔認証システム連携

---

**開発者:** Claude Code Assistant
**バージョン:** 1.0.0
**最終更新:** 2024年9月16日