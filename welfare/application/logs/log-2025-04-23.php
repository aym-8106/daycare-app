<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-04-23 12:23:32 --> Severity: Notice --> Trying to access array offset on value of type null D:\xampp\htdocs\welfare\application\models\Attendance_model.php 71
ERROR - 2025-04-23 12:54:03 --> Severity: Notice --> Trying to access array offset on value of type null D:\xampp\htdocs\welfare\application\models\Attendance_model.php 71
ERROR - 2025-04-23 13:45:34 --> Severity: Notice --> Undefined index: staff_mail_address D:\xampp\htdocs\welfare\application\views\admin\staff\index.php 101
ERROR - 2025-04-23 13:45:34 --> Severity: Notice --> Undefined index: staff_mail_address D:\xampp\htdocs\welfare\application\views\admin\staff\index.php 101
ERROR - 2025-04-23 13:45:34 --> Severity: Notice --> Undefined index: staff_mail_address D:\xampp\htdocs\welfare\application\views\admin\staff\index.php 101
ERROR - 2025-04-23 13:45:34 --> Severity: Notice --> Undefined index: staff_mail_address D:\xampp\htdocs\welfare\application\views\admin\staff\index.php 101
ERROR - 2025-04-23 14:04:56 --> Severity: Notice --> Undefined index: company_id D:\xampp\htdocs\welfare\application\controllers\Login.php 74
ERROR - 2025-04-23 14:06:44 --> Severity: Notice --> Undefined variable: loginInfo D:\xampp\htdocs\welfare\application\controllers\Login.php 67
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patient_usefrom D:\xampp\htdocs\welfare\application\views\instruction\add.php 116
ERROR - 2025-04-23 14:42:26 --> Severity: Notice --> Undefined index: patient_useto D:\xampp\htdocs\welfare\application\views\instruction\add.php 126
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:43:51 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: patientId D:\xampp\htdocs\welfare\application\views\instruction\add.php 68
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 14:43:56 --> Severity: Notice --> Undefined index: company D:\xampp\htdocs\welfare\application\views\instruction\add.php 84
ERROR - 2025-04-23 18:11:37 --> Query error: Unknown column 'patient_curetype_3' in 'where clause' - Invalid query: SELECT `id`, `patient_name`, (patient_curetype3) as patient_curetype, (patient_usefrom3) as patient_usefrom, (patient_useto3) as patient_useto, (patient_repeat3) as patient_repeat
FROM `tbl_patient`
WHERE `del_flag` = 0
AND id NOT IN (SELECT patient_id FROM tbl_schedule WHERE schedule_date = '2025-04-23' AND del_flag = 0)
AND `patient_curetype_3` != 0
AND `patient_usefrom_3` != ''
AND `patient_useto_3` != ''
AND `patient_repeat_3` != 0
ERROR - 2025-04-23 18:12:10 --> Query error: Unknown column 'patient_curetype_3' in 'field list' - Invalid query: SELECT `id`, `patient_name`, (patient_curetype_3) as patient_curetype, (patient_usefrom_3) as patient_usefrom, (patient_useto_3) as patient_useto, (patient_repeat_3) as patient_repeat
FROM `tbl_patient`
WHERE `del_flag` = 0
AND id NOT IN (SELECT patient_id FROM tbl_schedule WHERE schedule_date = '2025-04-23' AND del_flag = 0)
AND `patient_curetype_3` != 0
AND `patient_usefrom_3` != ''
AND `patient_useto_3` != ''
AND `patient_repeat_3` != 0
