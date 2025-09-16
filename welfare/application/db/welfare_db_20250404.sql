/*
 Navicat Premium Data Transfer

 Source Server         : localhost7.4
 Source Server Type    : MySQL
 Source Server Version : 100427 (10.4.27-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : welfare_db

 Target Server Type    : MySQL
 Target Server Version : 100427 (10.4.27-MariaDB)
 File Encoding         : 65001

 Date: 04/04/2025 07:27:14
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
  `last_activity` int UNSIGNED NOT NULL DEFAULT 0,
  `user_data` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`session_id`) USING BTREE,
  INDEX `last_activity_idx`(`last_activity` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ci_sessions
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_admin
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin`;
CREATE TABLE `tbl_admin`  (
  `admin_id` int NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `admin_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `del_flag` int NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`admin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_admin
-- ----------------------------
INSERT INTO `tbl_admin` VALUES (1, '管理者', 'admin@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 0, '2021-06-20 00:00:00', '2021-06-20 00:00:00');

-- ----------------------------
-- Table structure for tbl_company
-- ----------------------------
DROP TABLE IF EXISTS `tbl_company`;
CREATE TABLE `tbl_company`  (
  `company_id` int NOT NULL AUTO_INCREMENT COMMENT '会社ID',
  `company_email` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '会社名',
  `use_flag` int NOT NULL,
  `del_flag` int NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`company_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_company
-- ----------------------------
INSERT INTO `tbl_company` VALUES (1, 'company@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '53811ad1-9788-45b6-b00b-c7ecb9b5eb30', 'XXX事業所', 1, 0, '2021-06-20 00:00:00', '2025-02-03 14:35:55');
INSERT INTO `tbl_company` VALUES (2, 'a@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', '5d80a798-ab4d-47e2-a5c1-d9861e8eba35', 'a事業所', 0, 0, '2021-06-30 18:14:54', '2025-02-03 14:37:16');
INSERT INTO `tbl_company` VALUES (3, 'b@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'd211b2c8-1d28-4530-8f54-3f336451d31b', 'b事業所', 0, 0, '2021-06-30 18:15:47', '2025-02-03 14:36:55');
INSERT INTO `tbl_company` VALUES (4, 'c@example.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'e47d6f2a-c6ef-47dc-bd1c-3b773b523122', 'c事業所', 1, 0, '2021-06-30 18:17:50', '2025-02-03 14:37:06');

-- ----------------------------
-- Table structure for tbl_customer
-- ----------------------------
DROP TABLE IF EXISTS `tbl_customer`;
CREATE TABLE `tbl_customer`  (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `service_date` tinyint(1) NOT NULL,
  `service_time` time NOT NULL,
  `del_flag` tinyint(1) NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`customer_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_customer
-- ----------------------------
INSERT INTO `tbl_customer` VALUES (1, '4', 1, '00:00:00', 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00');

-- ----------------------------
-- Table structure for tbl_employtype
-- ----------------------------
DROP TABLE IF EXISTS `tbl_employtype`;
CREATE TABLE `tbl_employtype`  (
  `employtypeId` tinyint NOT NULL,
  `employtype` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`employtypeId`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_employtype
-- ----------------------------
INSERT INTO `tbl_employtype` VALUES (1, '常勤');
INSERT INTO `tbl_employtype` VALUES (2, '非常勤');

-- ----------------------------
-- Table structure for tbl_jobtype
-- ----------------------------
DROP TABLE IF EXISTS `tbl_jobtype`;
CREATE TABLE `tbl_jobtype`  (
  `jobtypeId` tinyint NOT NULL,
  `jobtype` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`jobtypeId`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_jobtype
-- ----------------------------
INSERT INTO `tbl_jobtype` VALUES (1, 'Ns');
INSERT INTO `tbl_jobtype` VALUES (2, 'リハビリ');
INSERT INTO `tbl_jobtype` VALUES (3, '準看護');

-- ----------------------------
-- Table structure for tbl_last_login
-- ----------------------------
DROP TABLE IF EXISTS `tbl_last_login`;
CREATE TABLE `tbl_last_login`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `company_id` bigint NOT NULL,
  `staff_id` bigint NOT NULL,
  `sessionData` varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `machineIp` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `userAgent` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `agentString` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `platform` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `createdDtm` datetime NOT NULL DEFAULT current_timestamp,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_last_login
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_reset_password
-- ----------------------------
DROP TABLE IF EXISTS `tbl_reset_password`;
CREATE TABLE `tbl_reset_password`  (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `email` varchar(128) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `activation_id` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `agent` varchar(512) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `client_ip` varchar(32) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `isDeleted` tinyint NOT NULL DEFAULT 0,
  `createdBy` bigint NOT NULL DEFAULT 1,
  `createdDtm` datetime NOT NULL,
  `updatedBy` bigint NULL DEFAULT NULL,
  `updatedDtm` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_reset_password
-- ----------------------------

-- ----------------------------
-- Table structure for tbl_roles
-- ----------------------------
DROP TABLE IF EXISTS `tbl_roles`;
CREATE TABLE `tbl_roles`  (
  `roleId` tinyint NOT NULL AUTO_INCREMENT COMMENT 'role id',
  `role` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'role text',
  PRIMARY KEY (`roleId`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_roles
-- ----------------------------
INSERT INTO `tbl_roles` VALUES (1, '管理者');
INSERT INTO `tbl_roles` VALUES (2, 'スタッフ');
INSERT INTO `tbl_roles` VALUES (3, '利用者');

-- ----------------------------
-- Table structure for tbl_setting
-- ----------------------------
DROP TABLE IF EXISTS `tbl_setting`;
CREATE TABLE `tbl_setting`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL COMMENT '会社ID',
  `key_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `key_value` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `user_id`(`company_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

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
-- Table structure for tbl_staff
-- ----------------------------
DROP TABLE IF EXISTS `tbl_staff`;
CREATE TABLE `tbl_staff`  (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `staff_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `staff_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `staff_role` tinyint NOT NULL,
  `staff_jobtype` tinyint NOT NULL,
  `staff_employtype` tinyint NOT NULL,
  `del_flag` int NOT NULL DEFAULT 0,
  `create_date` datetime NOT NULL,
  `update_date` datetime NOT NULL,
  PRIMARY KEY (`staff_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_staff
-- ----------------------------
INSERT INTO `tbl_staff` VALUES (1, 1, 'A', '8cb2237d0679ca88db6464eac60da96345513964', 2, 1, 1, 0, '2025-02-03 11:24:04', '2025-02-03 14:27:32');

-- ----------------------------
-- Table structure for tbl_users
-- ----------------------------
DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE `tbl_users`  (
  `userId` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `email` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'login email',
  `password` varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'hashed login password',
  `name` varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'full name of user',
  `mobile` varchar(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL,
  `roleId` tinyint NOT NULL,
  `work_start` int NOT NULL DEFAULT 9 COMMENT '作業開始時間',
  `work_end` int NOT NULL DEFAULT 17 COMMENT '作業終了時間',
  `wix_url` varchar(1025) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'WIX_URL',
  `wix_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'WIX_API_KEY',
  `wix_secret` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'WIX_API_SECRET',
  `isDeleted` tinyint NOT NULL DEFAULT 0,
  `createdBy` int NOT NULL,
  `createdDtm` datetime NOT NULL,
  `updatedBy` int NULL DEFAULT NULL,
  `updatedDtm` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`userId`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 11 CHARACTER SET = utf8 COLLATE = utf8_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tbl_users
-- ----------------------------
INSERT INTO `tbl_users` VALUES (1, 0, 'admin@example.com', '$2y$10$z7aiNveI4ebBr/st27oSuemwKs9pNoyyLkb/Os7/yKjrmZSKgY7d2', 'システム管理者', '0000000000', 99, 7, 21, 'sapporo', 'fea1272d-3c03-4055-9d2c-a390c246c329', 'GsAniOm010qMsEPCtzrn3dhu0TnkeXQTRzWljbFqS0w', 0, 0, '2015-07-01 18:56:49', 1, '2021-05-20 08:09:39');
INSERT INTO `tbl_users` VALUES (2, 0, 'user1@example.com', '$2y$10$FLHUy.v2QKHRV3aG7OWx9eqiXagKUjNOp/KxQL1Sgd7ATU9ulFWGS', 'ユーザー1', '9890098900', 2, 0, 0, '', '', '', 0, 1, '2016-12-09 17:49:56', 1, '2021-05-20 03:36:38');
INSERT INTO `tbl_users` VALUES (3, 0, 'staff1@example.com', '$2y$10$rN5humYKvUMGmlkWOwrcguSkXjnP2X5SV0XOpkPLiR9bNYwqZpKva', 'A', '9890098900', 2, 0, 0, '', '', '', 0, 1, '2016-12-09 17:50:22', 1, '2021-05-20 17:50:55');
INSERT INTO `tbl_users` VALUES (9, 0, 'mogawa@welfare.jp', '$2y$10$V2XQX1ut4PKrUwvhJsM0/O4QF1lbtdkESfCFOOTYYzWIGBi/67Lg.', '小川', '0357844515', 2, 0, 0, '', '', '', 0, 1, '2021-05-24 09:31:38', NULL, NULL);
INSERT INTO `tbl_users` VALUES (10, 0, 'na@welfare.jp', '$2y$10$XGM0mKe17STXPDoefmmrfuUr86TolP84W52SA6XkTmu.GDXHz9gia', 'na', '7015221100', 2, 0, 0, '', '', '', 0, 1, '2021-05-29 12:47:27', 1, '2021-06-18 09:21:34');

SET FOREIGN_KEY_CHECKS = 1;
