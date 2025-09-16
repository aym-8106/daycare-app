<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-06-15 02:23:55 --> Severity: error --> Exception: Call to undefined method Staff_model::loginMe() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Login.php 43
ERROR - 2025-06-15 02:33:12 --> Severity: Notice --> Undefined variable: start_time E:\xampp_7.4.1\htdocs\welfare\application\views\attendance\index.php 227
ERROR - 2025-06-15 10:28:55 --> Severity: User Error --> Composer detected issues in your platform: Your Composer dependencies require a PHP version ">= 8.2.0". You are running 7.4.1. E:\xampp_7.4.1\htdocs\welfare\vendor\composer\platform_check.php 28
ERROR - 2025-06-15 14:34:49 --> Query error: Column 'staff_id' in where clause is ambiguous - Invalid query: SELECT DATE_FORMAT(work_date, '%e') AS work_date, DATE_FORMAT(work_time, '%H:%i') AS work_time, DATE_FORMAT(leave_time, '%H:%i') AS leave_time, DATE_FORMAT(overtime_start_time, '%H:%i') AS overtime_start_time, DATE_FORMAT(overtime_end_time, '%H:%i') AS overtime_end_time, (break_time + overtime_break_time) AS total_break_time, TIMEDIFF(overtime_end_time, overtime_start_time) AS overtime_duration, `Staff`.`relax_time`
FROM `tbl_attendance`
LEFT JOIN `tbl_setting_staff` as `Staff` ON `Staff`.`staff_id` = `tbl_attendance`.`staff_id`
WHERE `staff_id` = '1'
AND `work_date` >= '2025-06-01'
AND `work_date` <= '2025-06-30'
ORDER BY `work_date` ASC
ERROR - 2025-06-15 14:35:17 --> Query error: Unknown column 'tbl_attendancestaff_id' in 'where clause' - Invalid query: SELECT DATE_FORMAT(work_date, '%e') AS work_date, DATE_FORMAT(work_time, '%H:%i') AS work_time, DATE_FORMAT(leave_time, '%H:%i') AS leave_time, DATE_FORMAT(overtime_start_time, '%H:%i') AS overtime_start_time, DATE_FORMAT(overtime_end_time, '%H:%i') AS overtime_end_time, (break_time + overtime_break_time) AS total_break_time, TIMEDIFF(overtime_end_time, overtime_start_time) AS overtime_duration, `Staff`.`relax_time`
FROM `tbl_attendance`
LEFT JOIN `tbl_setting_staff` as `Staff` ON `Staff`.`staff_id` = `tbl_attendance`.`staff_id`
WHERE `tbl_attendancestaff_id` = '1'
AND `work_date` >= '2025-06-01'
AND `work_date` <= '2025-06-30'
ORDER BY `work_date` ASC
ERROR - 2025-06-15 14:41:05 --> Severity: Notice --> Undefined variable: start_time E:\xampp_7.4.1\htdocs\welfare\application\views\attendance\index.php 232
ERROR - 2025-06-15 15:08:49 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\views\attendance\index.php 234
ERROR - 2025-06-15 15:08:49 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\views\attendance\index.php 241
ERROR - 2025-06-15 15:17:53 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\models\Attendance_model.php 71
ERROR - 2025-06-15 15:19:59 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\models\Attendance_model.php 71
ERROR - 2025-06-15 15:26:34 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\models\Attendance_model.php 71
ERROR - 2025-06-15 15:26:38 --> Severity: Notice --> Trying to access array offset on value of type null E:\xampp_7.4.1\htdocs\welfare\application\models\Attendance_model.php 71
