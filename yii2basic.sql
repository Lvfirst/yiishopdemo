/*
Navicat MySQL Data Transfer

Source Server         : xampp
Source Server Version : 50505
Source Host           : localhost:3306
Source Database       : yii2basic

Target Server Type    : MYSQL
Target Server Version : 50505
File Encoding         : 65001

Date: 2017-11-17 17:22:49
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for country
-- ----------------------------
DROP TABLE IF EXISTS `country`;
CREATE TABLE `country` (
  `code` char(2) NOT NULL,
  `name` char(52) NOT NULL,
  `population` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of country
-- ----------------------------
INSERT INTO `country` VALUES ('01', '我爱学习', '123215');
INSERT INTO `country` VALUES ('12', '2213', '9899');
INSERT INTO `country` VALUES ('AU', 'Australia', '18886000');
INSERT INTO `country` VALUES ('CA', 'Canada', '1147000');
INSERT INTO `country` VALUES ('CN', 'China', '1277558000');
INSERT INTO `country` VALUES ('DE', 'Germany', '82164700');
INSERT INTO `country` VALUES ('FR', 'France', '59225700');
INSERT INTO `country` VALUES ('GB', 'United Kingdom', '59623400');
INSERT INTO `country` VALUES ('IN', 'India', '1013662000');
INSERT INTO `country` VALUES ('RU', 'Russia', '146934000');
INSERT INTO `country` VALUES ('US', 'USA', '278357000');

-- ----------------------------
-- Table structure for migration
-- ----------------------------
DROP TABLE IF EXISTS `migration`;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of migration
-- ----------------------------
INSERT INTO `migration` VALUES ('m000000_000000_base', '1501326902');
