<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2025-04-11 00:26:29 --> Severity: Notice --> Undefined variable: staff_count E:\xampp_7.4.1\htdocs\welfare\application\views\admin\dashboard.php 30
ERROR - 2025-04-11 10:11:07 --> Query error: Unknown column 'tbl_post.patient_id' in 'on clause' - Invalid query: SELECT `tbl_post`.*, `tbl_patient`.`patient_name`, `tbl_staff`.`staff_name`
FROM `tbl_post`
LEFT JOIN `tbl_patient` ON `tbl_post`.`patient_id` = `tbl_patient`.`id`
LEFT JOIN `tbl_staff` ON `tbl_post`.`staff_id` = `tbl_staff`.`staff_id`
WHERE `tbl_post`.`del_flag` = 0
ORDER BY `tbl_post`.`post_date` ASC
