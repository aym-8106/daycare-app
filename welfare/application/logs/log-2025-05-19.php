<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-05-19 10:55:28 --> Severity: Compile Error --> Cannot redeclare Company_model::login() D:\xampp74\htdocs\welfare\application\models\Company_model.php 143
ERROR - 2025-05-19 10:58:19 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 10:58:19 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 10:59:18 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 10:59:18 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:00:30 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:00:31 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:00:32 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:00:32 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:00:36 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:00:36 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:01:12 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:01:12 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:04:28 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\company\includes\header.php 67
ERROR - 2025-05-19 11:04:28 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\company\includes\header.php 76
ERROR - 2025-05-19 11:06:49 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\company\includes\header.php 67
ERROR - 2025-05-19 11:06:49 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\company\includes\header.php 76
ERROR - 2025-05-19 11:07:32 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\company\includes\header.php 76
ERROR - 2025-05-19 11:14:27 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:14:27 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:15:55 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:15:55 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:16:02 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 67
ERROR - 2025-05-19 11:16:02 --> Severity: Notice --> Undefined index: admin_name D:\xampp74\htdocs\welfare\application\views\admin\includes\header.php 76
ERROR - 2025-05-19 11:28:10 --> Query error: Column 'company_id' in where clause is ambiguous - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbl_staff` as `BaseTbl`
LEFT JOIN `tbl_company` as `Company` ON `Company`.`company_id` = `BaseTbl`.`company_id`
LEFT JOIN `tbl_roles` as `Role` ON `Role`.`roleId` = `BaseTbl`.`staff_role`
LEFT JOIN `tbl_jobtype` as `Jobtype` ON `Jobtype`.`jobtypeId` = `BaseTbl`.`staff_jobtype`
LEFT JOIN `tbl_employtype` as `Employtype` ON `Employtype`.`employtypeId` = `BaseTbl`.`staff_employtype`
WHERE (`BaseTbl`.`staff_name` LIKE '%%'
                                    OR  `Company`.`company_name` LIKE '%%'
                                    OR  `BaseTbl`.`staff_mail_address` LIKE '%%')
AND `company_id` = '1'
AND `BaseTbl`.`del_flag` = 0
ERROR - 2025-05-19 11:29:25 --> Severity: Notice --> Array to string conversion D:\xampp74\htdocs\welfare\application\views\company\staff\index.php 56
ERROR - 2025-05-19 11:30:55 --> Severity: Notice --> Array to string conversion D:\xampp74\htdocs\welfare\application\views\company\staff\index.php 56
ERROR - 2025-05-19 11:30:56 --> Severity: Notice --> Array to string conversion D:\xampp74\htdocs\welfare\application\views\company\staff\index.php 56
