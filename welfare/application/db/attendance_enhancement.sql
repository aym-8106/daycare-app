-- 介護現場向け勤怠管理システムのデータベース拡張

-- 勤怠テーブルの拡張（既存のtbl_attendanceに追加カラム）
ALTER TABLE `tbl_attendance`
ADD COLUMN `location` VARCHAR(255) NULL COMMENT '勤務場所（GPS情報等）',
ADD COLUMN `check_in_photo` VARCHAR(255) NULL COMMENT 'チェックイン時の写真',
ADD COLUMN `check_out_photo` VARCHAR(255) NULL COMMENT 'チェックアウト時の写真',
ADD COLUMN `memo` TEXT NULL COMMENT '勤務メモ・特記事項',
ADD COLUMN `approval_status` TINYINT DEFAULT 0 COMMENT '承認状況（0:未承認, 1:承認済み, 2:却下）',
ADD COLUMN `approved_by` INT NULL COMMENT '承認者ID',
ADD COLUMN `approved_at` TIMESTAMP NULL COMMENT '承認日時',
ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '作成日時',
ADD COLUMN `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新日時';

-- 休暇申請テーブル
CREATE TABLE IF NOT EXISTS `tbl_leave_request` (
  `leave_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `leave_type` TINYINT NOT NULL COMMENT '休暇種別（1:有給, 2:病気休暇, 3:特別休暇, 4:その他）',
  `start_date` DATE NOT NULL COMMENT '休暇開始日',
  `end_date` DATE NOT NULL COMMENT '休暇終了日',
  `start_time` TIME NULL COMMENT '開始時刻（半日休暇用）',
  `end_time` TIME NULL COMMENT '終了時刻（半日休暇用）',
  `reason` TEXT NOT NULL COMMENT '休暇理由',
  `status` TINYINT DEFAULT 0 COMMENT '承認状況（0:申請中, 1:承認済み, 2:却下）',
  `approved_by` int(11) NULL COMMENT '承認者ID',
  `approved_at` TIMESTAMP NULL COMMENT '承認日時',
  `rejection_reason` TEXT NULL COMMENT '却下理由',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`leave_id`),
  KEY `idx_staff_date` (`staff_id`, `start_date`),
  FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff`(`staff_id`),
  FOREIGN KEY (`approved_by`) REFERENCES `tbl_staff`(`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='休暇申請テーブル';

-- 勤務パターンマスタテーブル
CREATE TABLE IF NOT EXISTS `tbl_work_pattern` (
  `pattern_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `pattern_name` VARCHAR(100) NOT NULL COMMENT '勤務パターン名（日勤、夜勤、早番、遅番等）',
  `start_time` TIME NOT NULL COMMENT '勤務開始時刻',
  `end_time` TIME NOT NULL COMMENT '勤務終了時刻',
  `break_duration` INT DEFAULT 60 COMMENT '休憩時間（分）',
  `color_code` VARCHAR(7) DEFAULT '#3498db' COMMENT '表示色',
  `is_active` TINYINT DEFAULT 1 COMMENT '有効フラグ',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`pattern_id`),
  KEY `idx_company` (`company_id`),
  FOREIGN KEY (`company_id`) REFERENCES `tbl_company`(`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='勤務パターンマスタ';

-- 勤務予定テーブル
CREATE TABLE IF NOT EXISTS `tbl_work_schedule` (
  `schedule_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `work_date` DATE NOT NULL,
  `pattern_id` int(11) NULL COMMENT '勤務パターンID',
  `scheduled_start` TIME NULL COMMENT '予定開始時刻',
  `scheduled_end` TIME NULL COMMENT '予定終了時刻',
  `memo` TEXT NULL COMMENT 'スケジュールメモ',
  `created_by` int(11) NOT NULL COMMENT '作成者ID',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`schedule_id`),
  UNIQUE KEY `unique_staff_date` (`staff_id`, `work_date`),
  KEY `idx_date` (`work_date`),
  FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff`(`staff_id`),
  FOREIGN KEY (`pattern_id`) REFERENCES `tbl_work_pattern`(`pattern_id`),
  FOREIGN KEY (`created_by`) REFERENCES `tbl_staff`(`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='勤務予定テーブル';

-- 勤怠修正申請テーブル
CREATE TABLE IF NOT EXISTS `tbl_attendance_correction` (
  `correction_id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `correction_type` TINYINT NOT NULL COMMENT '修正種別（1:出勤時刻, 2:退勤時刻, 3:休憩時間, 4:その他）',
  `original_value` VARCHAR(255) NULL COMMENT '修正前の値',
  `corrected_value` VARCHAR(255) NOT NULL COMMENT '修正後の値',
  `reason` TEXT NOT NULL COMMENT '修正理由',
  `status` TINYINT DEFAULT 0 COMMENT '承認状況（0:申請中, 1:承認済み, 2:却下）',
  `approved_by` int(11) NULL COMMENT '承認者ID',
  `approved_at` TIMESTAMP NULL COMMENT '承認日時',
  `rejection_reason` TEXT NULL COMMENT '却下理由',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`correction_id`),
  KEY `idx_attendance` (`attendance_id`),
  KEY `idx_staff` (`staff_id`),
  FOREIGN KEY (`attendance_id`) REFERENCES `tbl_attendance`(`attendance_id`),
  FOREIGN KEY (`staff_id`) REFERENCES `tbl_staff`(`staff_id`),
  FOREIGN KEY (`approved_by`) REFERENCES `tbl_staff`(`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='勤怠修正申請テーブル';

-- 初期データ挿入：標準的な勤務パターン
INSERT INTO `tbl_work_pattern` (`company_id`, `pattern_name`, `start_time`, `end_time`, `break_duration`, `color_code`) VALUES
(1, '日勤', '09:00:00', '18:00:00', 60, '#3498db'),
(1, '早番', '07:00:00', '16:00:00', 60, '#e74c3c'),
(1, '遅番', '11:00:00', '20:00:00', 60, '#f39c12'),
(1, '夜勤', '22:00:00', '07:00:00', 120, '#9b59b6'),
(1, '長日勤', '08:00:00', '20:00:00', 90, '#2ecc71');