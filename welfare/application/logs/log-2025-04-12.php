<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-04-12 10:06:53 --> Query error: Unknown column 'tbl_post.patient_usefrom' in 'order clause' - Invalid query: SELECT `tbl_post`.*, (tbl_patient.id) AS patient_id, `tbl_patient`.`patient_name`, `tbl_patient`.`patient_curetype`, `tbl_staff`.`staff_name`
FROM `tbl_post`
LEFT JOIN `tbl_patient` ON `tbl_post`.`patient_id` = `tbl_patient`.`id`
LEFT JOIN `tbl_staff` ON `tbl_post`.`staff_id` = `tbl_staff`.`staff_id`
WHERE `tbl_post`.`staff_id` = '1'
AND `tbl_post`.`post_date` = '2025-04-12'
AND `tbl_post`.`del_flag` = 0
ORDER BY `tbl_post`.`patient_usefrom` ASC, `tbl_patient`.`patient_curetype` ASC
ERROR - 2025-04-12 23:35:13 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:35:13 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:35:13 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:35:13 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:54:42 --> Severity: Notice --> Undefined variable: patient_regdate E:\xampp_7.4.1\htdocs\welfare\application\views\setting\patient\add.php 73
ERROR - 2025-04-12 23:57:03 --> Severity: Notice --> Undefined variable: patient_regdate E:\xampp_7.4.1\htdocs\welfare\application\views\setting\patient\edit.php 70
ERROR - 2025-04-12 23:59:16 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:59:16 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:59:16 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
ERROR - 2025-04-12 23:59:16 --> Severity: error --> Exception: Call to undefined method Patient_model::get_today_data() E:\xampp_7.4.1\htdocs\welfare\application\controllers\Schedule.php 65
