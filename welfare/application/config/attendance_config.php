<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| 勤怠管理システム設定
|--------------------------------------------------------------------------
*/

// 勤務時間の設定
$config['work_start_time'] = '09:00:00';
$config['work_end_time'] = '18:00:00';
$config['break_time_default'] = 60; // 分

// 遅刻の基準時間
$config['late_threshold_minutes'] = 30;

// 残業の自動判定基準
$config['overtime_threshold_minutes'] = 30;

// 位置情報の検証
$config['location_validation_enabled'] = false;
$config['allowed_location_radius'] = 500; // メートル

// 写真アップロード設定
$config['photo_upload_enabled'] = true;
$config['photo_max_size'] = 2048; // KB
$config['photo_upload_path'] = './uploads/attendance_photos/';

// QRコード設定
$config['qr_code_enabled'] = true;
$config['qr_code_expiry_hours'] = 24;

// 自動承認設定
$config['auto_approve_normal_punch'] = true;
$config['require_approval_for_corrections'] = true;

// 通知設定
$config['email_notifications'] = [
    'late_arrival' => true,
    'long_overtime' => true,
    'missing_punch' => true,
    'correction_request' => true
];

// アラート設定のデフォルト値
$config['default_alert_settings'] = [
    'late_threshold' => 30,
    'overtime_threshold' => 120,
    'continuous_absence_threshold' => 3,
    'email_notifications' => true
];

// 勤務パターン
$config['work_patterns'] = [
    'day_shift' => [
        'name' => '日勤',
        'start_time' => '09:00:00',
        'end_time' => '18:00:00',
        'break_duration' => 60,
        'color' => '#3498db'
    ],
    'early_shift' => [
        'name' => '早番',
        'start_time' => '07:00:00',
        'end_time' => '16:00:00',
        'break_duration' => 60,
        'color' => '#e74c3c'
    ],
    'late_shift' => [
        'name' => '遅番',
        'start_time' => '11:00:00',
        'end_time' => '20:00:00',
        'break_duration' => 60,
        'color' => '#f39c12'
    ],
    'night_shift' => [
        'name' => '夜勤',
        'start_time' => '22:00:00',
        'end_time' => '07:00:00',
        'break_duration' => 120,
        'color' => '#9b59b6'
    ]
];

// エクスポート設定
$config['export_formats'] = ['csv', 'excel', 'pdf'];
$config['max_export_records'] = 10000;

// システム設定
$config['attendance_retention_days'] = 2555; // 約7年間
$config['log_retention_days'] = 365;
$config['backup_enabled'] = true;

// セキュリティ設定
$config['max_punch_attempts_per_day'] = 10;
$config['ip_restriction_enabled'] = false;
$config['allowed_ip_ranges'] = [];

// API設定
$config['api_enabled'] = true;
$config['api_rate_limit'] = 1000; // 1時間あたりのリクエスト数

// 休暇設定
$config['leave_types'] = [
    1 => '有給休暇',
    2 => '病気休暇',
    3 => '特別休暇',
    4 => '代休',
    5 => '振替休日',
    6 => 'その他'
];

// 修正申請の種別
$config['correction_types'] = [
    1 => '出勤時刻',
    2 => '退勤時刻',
    3 => '休憩時間',
    4 => '勤務場所',
    5 => 'その他'
];

// 承認ステータス
$config['approval_status'] = [
    0 => '申請中',
    1 => '承認済み',
    2 => '却下'
];

?>