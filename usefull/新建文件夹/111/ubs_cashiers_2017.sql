/*
Navicat MySQL Data Transfer

Source Server         : cp6686
Source Server Version : 50719
Source Host           : 10.99.0.11:3306
Source Database       : cp6686me

Target Server Type    : MYSQL
Target Server Version : 50719
File Encoding         : 65001

Date: 2017-12-31 12:42:59
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ubs_cashiers_2017
-- ----------------------------
DROP TABLE IF EXISTS `ubs_cashiers_2018`;
CREATE TABLE `ubs_cashiers_2017` (
  `cashier_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `cashier_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cashier_payment_id` int(10) unsigned NOT NULL COMMENT '入款\n	\n出款\n	管理员ID',
  `cashier_relate_uid` int(10) unsigned NOT NULL COMMENT '入款\n	用户ID\n出款\n	用户ID',
  `cashier_relate_amount` decimal(20,2) NOT NULL COMMENT '入款\n	金额\n出款\n	金额',
  `cashier_relate_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '入款\n	0	等待\n	1	已处理\n	2	异常 ?? 未知\n出款\n	100	等待\n	101	已处理\n	102	异常 ?? 未知',
  `cashier_ext_info` json DEFAULT NULL,
  `cashier_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cashier_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cashier_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
