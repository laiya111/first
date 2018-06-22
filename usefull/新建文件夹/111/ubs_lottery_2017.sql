/*
Navicat MySQL Data Transfer

Source Server         : 103.193.174.92
Source Server Version : 50720
Source Host           : localhost:3306
Source Database       : newcp

Target Server Type    : MYSQL
Target Server Version : 50720
File Encoding         : 65001

Date: 2017-12-31 12:45:55
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for ubs_lottery_2018
-- ----------------------------
DROP TABLE IF EXISTS `ubs_lottery_2018`;
CREATE TABLE `ubs_lottery_2018` (
  `lottery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `lottery_class` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '彩种-分类',
  `lottery_round` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '彩种-期数',
  `lottery_round_extra` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '彩种-期数-扩展',
  `lottery_time` json NOT NULL COMMENT '彩种-开盘, 封盘-时间',
  `lottery_numbers` json NOT NULL COMMENT '彩种-开奖号码',
  `lottery_is_cancel` tinyint(4) NOT NULL DEFAULT '0' COMMENT '彩种-是否取消',
  `lottery_is_open` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `lottery_created_at` date NOT NULL COMMENT '彩种-创建时间-分区字段',
  `lottery_updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '彩种-更新时间',
  PRIMARY KEY (`lottery_id`),
  KEY `idx_lottery_class` (`lottery_class`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
