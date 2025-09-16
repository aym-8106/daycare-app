/*
 Navicat Premium Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 100411
 Source Host           : localhost:3306
 Source Schema         : welfare_db

 Target Server Type    : MySQL
 Target Server Version : 100411
 File Encoding         : 65001

 Date: 15/04/2025 08:45:18
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for ci_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE `ci_sessions`  (
  `session_id` varchar(40) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `ip_address` varchar(45) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '0',
  `user_agent` varchar(120) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `last_activity` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `user_data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`session_id`) USING BTREE,
  INDEX `last_activity_idx`(`last_activity`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tbl_admin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin`  (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_admin
-- ----------------------------
INSERT INTO `tbl_admin` VALUES (1, '管理者', 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 0, '2021-06-20 00:00:00', '2021-06-20 00:00:00');

-- ----------------------------
-- Table structure for tbl_attendance
-- ----------------------------
DROP TABLE IF EXISTS `tbl_attendance`;
CREATE TABLE `tbl_attendance`  (
  `attendance_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `work_time` datetime(0) NOT NULL,
  `leave_time` datetime(0) NOT NULL,
  `break_time` int(11) NOT NULL DEFAULT 0,
  `overtime_start_time` datetime(0) NOT NULL,
  `overtime_end_time` datetime(0) NOT NULL,
  `overtime_break_time` int(11) NOT NULL DEFAULT 0,
  `overtime_reason` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL DEFAULT current_timestamp(0),
  PRIMARY KEY (`attendance_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 63 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_attendance
-- ----------------------------
INSERT INTO `tbl_attendance` VALUES (46, 1, '2025-04-10', '2025-04-10 09:53:37', '2025-04-10 17:53:37', 60, '2025-04-10 19:00:10', '2025-04-10 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (47, 2, '2025-04-10', '2025-04-10 09:53:37', '2025-04-10 17:53:37', 60, '2025-04-10 19:00:10', '2025-04-10 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (48, 3, '2025-04-10', '2025-04-10 09:53:37', '2025-04-10 17:53:37', 60, '2025-04-10 19:00:10', '2025-04-10 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (49, 4, '2025-04-10', '2025-04-10 09:53:37', '2025-04-10 17:53:37', 60, '2025-04-10 19:00:10', '2025-04-10 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (50, 1, '2025-04-09', '2025-04-09 09:53:37', '2025-04-09 17:53:37', 60, '2025-04-09 19:00:10', '2025-04-09 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (51, 2, '2025-04-09', '2025-04-09 09:53:37', '2025-04-09 17:53:37', 60, '2025-04-09 19:00:10', '2025-04-09 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (52, 3, '2025-04-09', '2025-04-09 09:53:37', '2025-04-09 17:53:37', 60, '2025-04-09 19:00:10', '2025-04-09 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (53, 4, '2025-04-09', '2025-04-09 09:53:37', '2025-04-09 17:53:37', 60, '2025-04-09 19:00:10', '2025-04-09 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (55, 1, '2025-04-11', '2025-04-11 09:53:37', '2025-04-11 17:53:37', 60, '2025-04-11 19:00:10', '2025-04-11 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (56, 2, '2025-04-11', '2025-04-11 09:53:37', '2025-04-11 17:53:37', 60, '2025-04-11 19:00:10', '2025-04-11 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (57, 3, '2025-04-11', '2025-04-11 09:53:37', '2025-04-11 17:53:37', 60, '2025-04-11 19:00:10', '2025-04-11 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (58, 4, '2025-04-11', '2025-04-11 09:53:37', '2025-04-11 17:53:37', 60, '2025-04-11 19:00:10', '2025-04-11 21:35:30', 8, 'teset', '2025-04-09 09:51:25', '2025-04-09 09:51:25');
INSERT INTO `tbl_attendance` VALUES (59, 1, '2025-04-12', '2025-04-12 09:53:37', '2025-04-12 17:53:37', 60, '2025-04-12 19:00:10', '2025-04-12 21:35:30', 8, 'teset', '2025-04-12 09:51:25', '2025-04-12 09:51:25');
INSERT INTO `tbl_attendance` VALUES (60, 2, '2025-04-12', '2025-04-12 09:53:37', '2025-04-12 17:53:37', 60, '2025-04-12 19:00:10', '2025-04-12 21:35:30', 8, 'teset', '2025-04-12 09:51:25', '2025-04-12 09:51:25');
INSERT INTO `tbl_attendance` VALUES (61, 3, '2025-04-12', '2025-04-12 09:53:37', '2025-04-12 17:53:37', 60, '2025-04-12 19:00:10', '2025-04-12 21:35:30', 8, 'teset', '2025-04-12 09:51:25', '2025-04-12 09:51:25');
INSERT INTO `tbl_attendance` VALUES (62, 4, '2025-04-12', '2025-04-12 09:53:37', '2025-04-12 17:53:37', 60, '2025-04-12 19:00:10', '2025-04-12 21:35:30', 8, 'teset', '2025-04-12 09:51:25', '2025-04-12 09:51:25');

-- ----------------------------
-- Table structure for tbl_company
-- ----------------------------
DROP TABLE IF EXISTS `tbl_company`;
CREATE TABLE `tbl_company`  (
  `company_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '会社ID',
  `company_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会社名',
  `use_flag` int(11) NOT NULL,
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_company
-- ----------------------------
INSERT INTO `tbl_company` VALUES (1, 'ochiryo@gmail.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '53811ad1-9788-45b6-b00b-c7ecb9b5eb30', '横浜院', 1, 0, '2021-06-20 00:00:00', '2025-04-09 01:10:08');
INSERT INTO `tbl_company` VALUES (2, 'yokohama@gmail.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '5d80a798-ab4d-47e2-a5c1-d9861e8eba35', '大宮院', 1, 0, '2021-06-30 18:14:54', '2025-04-09 01:10:30');
INSERT INTO `tbl_company` VALUES (3, 'koriyama@gmail.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'd211b2c8-1d28-4530-8f54-3f336451d31b', '郡山院', 1, 0, '2021-06-30 18:15:47', '2025-04-09 01:10:52');
INSERT INTO `tbl_company` VALUES (4, 'c@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'e47d6f2a-c6ef-47dc-bd1c-3b773b523122', 'c事業所', 0, 1, '2021-06-30 18:17:50', '2025-02-03 14:37:06');

-- ----------------------------
-- Table structure for tbl_customer
-- ----------------------------
DROP TABLE IF EXISTS `tbl_customer`;
CREATE TABLE `tbl_customer`  (
  `customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `service_date` tinyint(1) NOT NULL,
  `service_time` time(0) NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`customer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_customer
-- ----------------------------
INSERT INTO `tbl_customer` VALUES (1, '4', 1, '00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tbl_employtype
-- ----------------------------
DROP TABLE IF EXISTS `tbl_employtype`;
CREATE TABLE `tbl_employtype`  (
  `employtypeId` tinyint(4) NOT NULL,
  `employtype` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`employtypeId`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_employtype
-- ----------------------------
INSERT INTO `tbl_employtype` VALUES (1, '常勤');
INSERT INTO `tbl_employtype` VALUES (2, '非常勤');

-- ----------------------------
-- Table structure for tbl_instruction
-- ----------------------------
DROP TABLE IF EXISTS `tbl_instruction`;
CREATE TABLE `tbl_instruction`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `instruction_start` date NULL DEFAULT NULL COMMENT '指示期間開始',
  `instruction_end` date NULL DEFAULT NULL COMMENT '指示期間終了',
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 46 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_instruction
-- ----------------------------
INSERT INTO `tbl_instruction` VALUES (38, 1, 1, '2025-04-14', '2025-04-30', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (39, 1, 34, '2025-04-14', '2025-04-30', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (40, 1, 1, '2025-04-14', '2025-04-30', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (41, 1, 1, '2025-04-14', '2025-04-30', 1, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (42, 1, 35, '2025-04-14', '2025-04-30', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (43, 1, 1, '2025-04-14', '2025-04-30', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (44, 2, 1, '2025-04-01', '2025-04-04', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_instruction` VALUES (45, 2, 35, '2025-05-07', '2025-05-15', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tbl_jobtype
-- ----------------------------
DROP TABLE IF EXISTS `tbl_jobtype`;
CREATE TABLE `tbl_jobtype`  (
  `jobtypeId` tinyint(4) NOT NULL,
  `jobtype` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`jobtypeId`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_jobtype
-- ----------------------------
INSERT INTO `tbl_jobtype` VALUES (1, '看護師');
INSERT INTO `tbl_jobtype` VALUES (2, 'PT');
INSERT INTO `tbl_jobtype` VALUES (3, '0T');
INSERT INTO `tbl_jobtype` VALUES (4, 'ST');
INSERT INTO `tbl_jobtype` VALUES (5, '准看護師');

-- ----------------------------
-- Table structure for tbl_last_login
-- ----------------------------
DROP TABLE IF EXISTS `tbl_last_login`;
CREATE TABLE `tbl_last_login`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `company_id` bigint(20) NOT NULL,
  `staff_id` bigint(20) NOT NULL,
  `sessionData` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `machineIp` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `userAgent` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `agentString` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `platform` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDtm` datetime(0) NOT NULL DEFAULT current_timestamp(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_last_login
-- ----------------------------
INSERT INTO `tbl_last_login` VALUES (1, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-05 23:40:41');
INSERT INTO `tbl_last_login` VALUES (2, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-06 01:23:37');
INSERT INTO `tbl_last_login` VALUES (3, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-06 20:40:18');
INSERT INTO `tbl_last_login` VALUES (4, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-06 23:28:01');
INSERT INTO `tbl_last_login` VALUES (5, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-07 19:35:09');
INSERT INTO `tbl_last_login` VALUES (6, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-07 23:24:23');
INSERT INTO `tbl_last_login` VALUES (7, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-08 09:11:08');
INSERT INTO `tbl_last_login` VALUES (8, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-08 11:47:56');
INSERT INTO `tbl_last_login` VALUES (9, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-08 19:53:09');
INSERT INTO `tbl_last_login` VALUES (10, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-08 22:07:04');
INSERT INTO `tbl_last_login` VALUES (11, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 134.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', 'Windows 10', '2025-04-09 00:35:05');
INSERT INTO `tbl_last_login` VALUES (12, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-09 01:15:03');
INSERT INTO `tbl_last_login` VALUES (13, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-09 09:47:00');
INSERT INTO `tbl_last_login` VALUES (14, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-09 11:49:14');
INSERT INTO `tbl_last_login` VALUES (15, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-09 17:30:55');
INSERT INTO `tbl_last_login` VALUES (16, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-09 19:37:36');
INSERT INTO `tbl_last_login` VALUES (17, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-09 22:45:47');
INSERT INTO `tbl_last_login` VALUES (18, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-10 01:21:33');
INSERT INTO `tbl_last_login` VALUES (19, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-10 09:15:57');
INSERT INTO `tbl_last_login` VALUES (20, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-10 11:47:33');
INSERT INTO `tbl_last_login` VALUES (21, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-10 21:09:28');
INSERT INTO `tbl_last_login` VALUES (22, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-10 23:09:46');
INSERT INTO `tbl_last_login` VALUES (23, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-11 01:10:01');
INSERT INTO `tbl_last_login` VALUES (24, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-11 10:00:48');
INSERT INTO `tbl_last_login` VALUES (25, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-11 16:18:50');
INSERT INTO `tbl_last_login` VALUES (26, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-11 18:19:04');
INSERT INTO `tbl_last_login` VALUES (27, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-12 10:06:43');
INSERT INTO `tbl_last_login` VALUES (28, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-12 22:41:39');
INSERT INTO `tbl_last_login` VALUES (29, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Safari 604.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.6 Mobile/15E148 Safari/604.1', 'iOS', '2025-04-13 00:43:45');
INSERT INTO `tbl_last_login` VALUES (30, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-14 02:14:37');
INSERT INTO `tbl_last_login` VALUES (31, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-14 18:09:15');
INSERT INTO `tbl_last_login` VALUES (32, 1, 1, '{\"company_id\":\"1\",\"staff_id\":\"1\",\"post\":{\"company_id\":\"1\",\"staff_id\":\"1\",\"staff_password\":\"12345\",\"remember\":null}}', '127.0.0.1', 'Chrome 135.0.0.0', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/135.0.0.0 Safari/537.36', 'Windows 10', '2025-04-15 08:42:26');

-- ----------------------------
-- Table structure for tbl_patient
-- ----------------------------
DROP TABLE IF EXISTS `tbl_patient`;
CREATE TABLE `tbl_patient`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `patient_addr` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
  `patient_regdate` date NOT NULL,
  `patient_date` int(11) NOT NULL DEFAULT 0 COMMENT '1:mon, 2:tue, 3:wed, 4:thu, 5:fri, 6:sat, 7:sun',
  `patient_curetype` int(11) NOT NULL DEFAULT 0 COMMENT '看護orリハビリ\r\n',
  `patient_usefrom` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'HH:mm',
  `patient_useto` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'HH:mm',
  `patient_repeat` int(11) NOT NULL DEFAULT 0 COMMENT '1:day, 2:week, 3:doubleweek, 4:month',
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 38 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_patient
-- ----------------------------
INSERT INTO `tbl_patient` VALUES (1, '吉田1', 'アドレス1', '2025-04-09', 1, 2, '10:00', '12:00', 1, 0, '2021-06-20 00:00:00', '2025-04-09 01:10:08');
INSERT INTO `tbl_patient` VALUES (34, '吉田2', 'アドレス2', '2025-04-09', 2, 1, '11:00', '12:00', 3, 0, '2021-06-20 00:00:00', '2025-04-09 01:10:08');
INSERT INTO `tbl_patient` VALUES (35, '吉田3', 'アドレス3', '2025-04-11', 4, 2, '10:00', '12:00', 1, 0, '2021-06-20 00:00:00', '2025-04-09 01:10:08');
INSERT INTO `tbl_patient` VALUES (36, '吉田4', 'アドレス4', '2025-04-08', 5, 1, '11:00', '12:00', 4, 0, '2021-06-20 00:00:00', '2025-04-09 01:10:08');

-- ----------------------------
-- Table structure for tbl_post
-- ----------------------------
DROP TABLE IF EXISTS `tbl_post`;
CREATE TABLE `tbl_post`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `post_date` date NOT NULL,
  `patient_usefrom` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `patient_useto` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `post_content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `del_flag` tinyint(1) NOT NULL,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_post
-- ----------------------------
INSERT INTO `tbl_post` VALUES (1, 1, 1, '2025-04-11', '13:00', '15:00', '123123123123213123213123123123123123123', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');
INSERT INTO `tbl_post` VALUES (2, 1, 34, '2025-04-12', '12:00', '16:00', '999', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tbl_reset_password
-- ----------------------------
DROP TABLE IF EXISTS `tbl_reset_password`;
CREATE TABLE `tbl_reset_password`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `activation_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `agent` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `client_ip` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` bigint(20) NOT NULL DEFAULT 1,
  `createdDtm` datetime(0) NOT NULL,
  `updatedBy` bigint(20) NULL DEFAULT NULL,
  `updatedDtm` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for tbl_roles
-- ----------------------------
DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles`  (
  `roleId` tinyint(4) NOT NULL AUTO_INCREMENT COMMENT 'role id',
  `role` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'role text',
  PRIMARY KEY (`roleId`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_roles
-- ----------------------------
INSERT INTO `tbl_roles` VALUES (1, '管理者');
INSERT INTO `tbl_roles` VALUES (2, 'スタッフ');
INSERT INTO `tbl_roles` VALUES (3, '利用者');

-- ----------------------------
-- Table structure for tbl_schedule
-- ----------------------------
DROP TABLE IF EXISTS `tbl_schedule`;
CREATE TABLE `tbl_schedule`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `schedule_start_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'HH:mm',
  `schedule_end_time` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'HH:mm',
  `del_flag` tinyint(1) NULL DEFAULT 0,
  `create_time` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  `update_time` timestamp(0) NOT NULL DEFAULT current_timestamp(0),
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 96 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_schedule
-- ----------------------------
INSERT INTO `tbl_schedule` VALUES (94, 1, 1, '2025-04-15', '10:30', '13:00', 0, '2025-04-15 08:42:41', '2025-04-15 08:42:41');
INSERT INTO `tbl_schedule` VALUES (95, 2, 34, '2025-04-15', '13:00', '15:30', 0, '2025-04-15 08:43:08', '2025-04-15 08:43:08');

-- ----------------------------
-- Table structure for tbl_setting
-- ----------------------------
DROP TABLE IF EXISTS `tbl_setting`;
CREATE TABLE `tbl_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL COMMENT '会社ID',
  `key_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `key_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_setting
-- ----------------------------
INSERT INTO `tbl_setting` VALUES (1, 1, 'faq_date', '2021-01-01');
INSERT INTO `tbl_setting` VALUES (2, 1, 'anal_date', '2021-01-01');
INSERT INTO `tbl_setting` VALUES (3, 3, 'anal_date', '2021-05-31');
INSERT INTO `tbl_setting` VALUES (4, 3, 'faq_date', '2021-05-31');
INSERT INTO `tbl_setting` VALUES (5, 4, 'anal_date', '2021-05-31');
INSERT INTO `tbl_setting` VALUES (6, 4, 'faq_date', '2021-05-31');

-- ----------------------------
-- Table structure for tbl_setting_staff
-- ----------------------------
DROP TABLE IF EXISTS `tbl_setting_staff`;
CREATE TABLE `tbl_setting_staff`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `mon_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `tue_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `wed_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `thu_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fri_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sat_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sun_start` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `mon_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `tue_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `wed_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `thu_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `fri_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sat_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `sun_end` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `relax_time` int(11) NULL DEFAULT NULL,
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_setting_staff
-- ----------------------------
INSERT INTO `tbl_setting_staff` VALUES (23, 1, 1, '09:00', '09:00', '09:00', '09:00', '09:00', '09:00', NULL, '18:00', '18:00', '18:00', '18:00', '18:00', '18:00', NULL, 60, 0, '2025-04-09 18:04:27', '2025-04-09 18:04:27');
INSERT INTO `tbl_setting_staff` VALUES (24, 2, 2, '09:00', '09:00', '09:00', '09:00', '09:00', '09:00', NULL, '18:00', '18:00', '18:00', '18:00', '18:00', '18:00', NULL, 60, 0, '2025-04-09 18:04:27', '2025-04-09 18:04:27');
INSERT INTO `tbl_setting_staff` VALUES (25, 3, 3, '09:00', '09:00', '09:00', '09:00', '09:00', '09:00', NULL, '18:00', '18:00', '18:00', '18:00', '18:00', '18:00', NULL, 60, 0, '2025-04-09 18:04:27', '2025-04-09 18:04:27');
INSERT INTO `tbl_setting_staff` VALUES (26, 1, 4, '09:00', '09:00', '09:00', '09:00', '09:00', '09:00', NULL, '18:00', '18:00', '18:00', '18:00', '18:00', '18:00', NULL, 60, 0, '2025-04-09 18:04:27', '2025-04-09 18:04:27');

-- ----------------------------
-- Table structure for tbl_staff
-- ----------------------------
DROP TABLE IF EXISTS `tbl_staff`;
CREATE TABLE `tbl_staff`  (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `staff_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `staff_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `staff_role` tinyint(4) NOT NULL,
  `staff_jobtype` tinyint(4) NOT NULL,
  `staff_employtype` tinyint(4) NOT NULL,
  `del_flag` int(11) NOT NULL DEFAULT 0,
  `create_date` datetime(0) NOT NULL,
  `update_date` datetime(0) NOT NULL,
  PRIMARY KEY (`staff_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_staff
-- ----------------------------
INSERT INTO `tbl_staff` VALUES (1, 1, '渡邉', '8cb2237d0679ca88db6464eac60da96345513964', 2, 1, 1, 0, '2025-02-03 11:24:04', '2025-04-09 01:12:27');
INSERT INTO `tbl_staff` VALUES (2, 2, '加藤', 'd033e22ae348aeb5660fc2140aec35850c4da997', 2, 2, 1, 0, '2025-04-05 13:03:28', '2025-04-09 01:12:49');
INSERT INTO `tbl_staff` VALUES (3, 3, '高浮', '8cb2237d0679ca88db6464eac60da96345513964', 2, 3, 1, 0, '2025-04-09 01:13:22', '2025-04-09 01:13:22');
INSERT INTO `tbl_staff` VALUES (4, 1, '富山', '8cb2237d0679ca88db6464eac60da96345513964', 2, 5, 1, 0, '2025-04-09 01:13:50', '2025-04-09 01:13:50');

-- ----------------------------
-- Table structure for tbl_users
-- ----------------------------
DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users`  (
  `userId` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'login email',
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hashed login password',
  `name` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'full name of user',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `roleId` tinyint(4) NOT NULL,
  `work_start` int(11) NOT NULL DEFAULT 9 COMMENT '作業開始時間',
  `work_end` int(11) NOT NULL DEFAULT 17 COMMENT '作業終了時間',
  `wix_url` varchar(1025) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'WIX_URL',
  `wix_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'WIX_API_KEY',
  `wix_secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'WIX_API_SECRET',
  `isDeleted` tinyint(4) NOT NULL DEFAULT 0,
  `createdBy` int(11) NOT NULL,
  `createdDtm` datetime(0) NOT NULL,
  `updatedBy` int(11) NULL DEFAULT NULL,
  `updatedDtm` datetime(0) NULL DEFAULT NULL,
  PRIMARY KEY (`userId`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbl_users
-- ----------------------------
INSERT INTO `tbl_users` VALUES (1, 0, 'admin@example.com', '$2y$10$z7aiNveI4ebBr/st27oSuemwKs9pNoyyLkb/Os7/yKjrmZSKgY7d2', 'システム管理者', '0000000000', 99, 7, 21, 'sapporo', 'fea1272d-3c03-4055-9d2c-a390c246c329', 'GsAniOm010qMsEPCtzrn3dhu0TnkeXQTRzWljbFqS0w', 0, 0, '2015-07-01 18:56:49', 1, '2021-05-20 08:09:39');
INSERT INTO `tbl_users` VALUES (2, 0, 'user1@example.com', '$2y$10$FLHUy.v2QKHRV3aG7OWx9eqiXagKUjNOp/KxQL1Sgd7ATU9ulFWGS', 'ユーザー1', '9890098900', 2, 0, 0, '', '', '', 0, 1, '2016-12-09 17:49:56', 1, '2021-05-20 03:36:38');
INSERT INTO `tbl_users` VALUES (3, 0, 'staff1@example.com', '$2y$10$rN5humYKvUMGmlkWOwrcguSkXjnP2X5SV0XOpkPLiR9bNYwqZpKva', 'A', '9890098900', 2, 0, 0, '', '', '', 0, 1, '2016-12-09 17:50:22', 1, '2021-05-20 17:50:55');
INSERT INTO `tbl_users` VALUES (9, 0, 'mogawa@welfare.jp', '$2y$10$V2XQX1ut4PKrUwvhJsM0/O4QF1lbtdkESfCFOOTYYzWIGBi/67Lg.', '小川', '0357844515', 2, 0, 0, '', '', '', 0, 1, '2021-05-24 09:31:38', NULL, NULL);
INSERT INTO `tbl_users` VALUES (10, 0, 'na@welfare.jp', '$2y$10$XGM0mKe17STXPDoefmmrfuUr86TolP84W52SA6XkTmu.GDXHz9gia', 'na', '7015221100', 2, 0, 0, '', '', '', 0, 1, '2021-05-29 12:47:27', 1, '2021-06-18 09:21:34');

SET FOREIGN_KEY_CHECKS = 1;
