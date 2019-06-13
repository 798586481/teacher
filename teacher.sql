/*
 Navicat Premium Data Transfer

 Source Server         : cdb-egd5cmgd.gz.tencentcdb.com_10035
 Source Server Type    : MySQL
 Source Server Version : 50718
 Source Host           : cdb-egd5cmgd.gz.tencentcdb.com:10035
 Source Schema         : teacher

 Target Server Type    : MySQL
 Target Server Version : 50718
 File Encoding         : 65001

 Date: 13/06/2019 14:55:57
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for t_bkllk
-- ----------------------------
DROP TABLE IF EXISTS `t_bkllk`;
CREATE TABLE `t_bkllk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kcmc` varchar(50) DEFAULT NULL COMMENT '课程名称',
  `sksj` varchar(10) DEFAULT NULL COMMENT '授课时间',
  `jhxs` varchar(5) DEFAULT NULL COMMENT '计划学时',
  `kcxs` varchar(5) DEFAULT NULL COMMENT '课程系数',
  `xyxs` varchar(5) DEFAULT NULL COMMENT '效益系数X',
  `mtgzl` varchar(5) DEFAULT NULL COMMENT '命题工作量',
  `jkgzl` varchar(5) DEFAULT NULL COMMENT '监考工作量',
  `cfbxs` varchar(5) DEFAULT NULL COMMENT '重复班系数',
  `bzxs` varchar(10) DEFAULT NULL COMMENT '标准学时',
  `uid` varchar(30) DEFAULT NULL COMMENT '老师uid',
  `status` int(5) DEFAULT '10' COMMENT '状态',
  `classesCourseId` int(11) DEFAULT NULL COMMENT '关联id',
  `courseId` int(11) DEFAULT NULL COMMENT '课程id，用来检测重复数据',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_classes
-- ----------------------------
DROP TABLE IF EXISTS `t_classes`;
CREATE TABLE `t_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '课程名称',
  `peoples` int(5) DEFAULT NULL COMMENT '班级人数',
  `status` int(5) NOT NULL DEFAULT '10' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_classes_course
-- ----------------------------
DROP TABLE IF EXISTS `t_classes_course`;
CREATE TABLE `t_classes_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `classesId` int(11) DEFAULT NULL COMMENT '班级id',
  `courseId` int(11) DEFAULT NULL COMMENT '课程id',
  `uid` char(20) DEFAULT NULL COMMENT '教师uid',
  PRIMARY KEY (`id`),
  KEY `class` (`classesId`),
  KEY `course` (`courseId`),
  CONSTRAINT `class` FOREIGN KEY (`classesId`) REFERENCES `t_classes` (`id`),
  CONSTRAINT `course` FOREIGN KEY (`courseId`) REFERENCES `t_course` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_course
-- ----------------------------
DROP TABLE IF EXISTS `t_course`;
CREATE TABLE `t_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '课程名称',
  `nature` varchar(50) NOT NULL COMMENT '课程性质',
  `status` int(10) DEFAULT NULL COMMENT '状态',
  `sksj` varchar(10) DEFAULT NULL COMMENT '授课时间',
  `jhxs` varchar(10) DEFAULT NULL COMMENT '课内学时',
  `kcbh` int(10) DEFAULT NULL COMMENT '课程编号',
  `credit` float(5,1) DEFAULT NULL COMMENT '学分',
  `knsy` int(4) DEFAULT NULL COMMENT '实验学时',
  `zxs` int(4) DEFAULT NULL COMMENT '总学时',
  `attributes` tinyint(4) DEFAULT NULL COMMENT '属性',
  `uid` char(20) DEFAULT NULL COMMENT '主教师的uid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_errorLog
-- ----------------------------
DROP TABLE IF EXISTS `t_errorLog`;
CREATE TABLE `t_errorLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `errorInfo` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `times` datetime DEFAULT NULL,
  `uid` varchar(50) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL,
  `lines` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=133 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_experiment
-- ----------------------------
DROP TABLE IF EXISTS `t_experiment`;
CREATE TABLE `t_experiment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) DEFAULT NULL,
  `numbering` int(10) DEFAULT NULL COMMENT '实验编号',
  `jhxs` int(5) DEFAULT NULL COMMENT '计划学时',
  `sksj` int(5) DEFAULT NULL COMMENT '授课时间',
  `years` int(4) DEFAULT NULL COMMENT '学年',
  `type` tinyint(4) DEFAULT NULL COMMENT '类型',
  `uid` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_experiment_classes
-- ----------------------------
DROP TABLE IF EXISTS `t_experiment_classes`;
CREATE TABLE `t_experiment_classes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `experimentId` int(11) DEFAULT NULL,
  `classesId` int(11) DEFAULT NULL,
  `uid` char(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `experimentId` (`experimentId`),
  KEY `classesId` (`classesId`),
  CONSTRAINT `classesId` FOREIGN KEY (`classesId`) REFERENCES `t_classes` (`id`),
  CONSTRAINT `experimentId` FOREIGN KEY (`experimentId`) REFERENCES `t_experiment` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_loginLog
-- ----------------------------
DROP TABLE IF EXISTS `t_loginLog`;
CREATE TABLE `t_loginLog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL COMMENT 'ip',
  `local` varchar(30) DEFAULT NULL COMMENT 'ip地点',
  `status` int(10) DEFAULT NULL COMMENT '登陆状态',
  `time` datetime DEFAULT NULL COMMENT '登陆时间',
  `isp` varchar(40) DEFAULT NULL COMMENT '运营商',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_menus
-- ----------------------------
DROP TABLE IF EXISTS `t_menus`;
CREATE TABLE `t_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `icon` varchar(100) DEFAULT NULL COMMENT '菜单图标',
  `urls` varchar(255) DEFAULT NULL,
  `urlType` int(5) NOT NULL DEFAULT '1' COMMENT '是否为外链，外链为2，默认为1',
  `status` int(5) NOT NULL DEFAULT '10' COMMENT '状态，10为正常，默认为10',
  `root` int(5) DEFAULT NULL COMMENT '根节点',
  `outh` int(10) DEFAULT NULL COMMENT '权限',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_system
-- ----------------------------
DROP TABLE IF EXISTS `t_system`;
CREATE TABLE `t_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL COMMENT '状态或者数值',
  `root` int(5) DEFAULT NULL,
  `beizhu` varchar(255) DEFAULT NULL,
  `msg` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_token
-- ----------------------------
DROP TABLE IF EXISTS `t_token`;
CREATE TABLE `t_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` varchar(100) DEFAULT NULL,
  `addTime` varchar(50) DEFAULT NULL COMMENT '生成token时间，时间戳',
  `expired` int(10) DEFAULT NULL COMMENT '过期时间，单位为秒',
  `uid` varchar(50) DEFAULT NULL COMMENT '相应教师的uid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_users
-- ----------------------------
DROP TABLE IF EXISTS `t_users`;
CREATE TABLE `t_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` varchar(50) NOT NULL COMMENT '用户标识',
  `name` varchar(10) NOT NULL COMMENT '真实姓名',
  `phone` varchar(20) DEFAULT NULL COMMENT '手机号',
  `token` varchar(255) DEFAULT NULL COMMENT 'token',
  `jwname` varchar(100) DEFAULT NULL COMMENT '教务用户名',
  `status` int(5) DEFAULT '10' COMMENT '状态',
  `preparedBy` int(5) DEFAULT '1' COMMENT '编制，默认1为行健教师',
  `position` tinyint(4) DEFAULT NULL COMMENT '职位',
  `pwd` varchar(100) DEFAULT NULL,
  `lastip` varchar(30) DEFAULT NULL COMMENT '上一次登录ip',
  `lasttime` varchar(30) DEFAULT NULL COMMENT '上一次登录时间',
  `errorNum` tinyint(4) DEFAULT NULL COMMENT '出错次数',
  `department` int(4) DEFAULT NULL COMMENT '部门',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_varible
-- ----------------------------
DROP TABLE IF EXISTS `t_varible`;
CREATE TABLE `t_varible` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(50) DEFAULT NULL COMMENT '功能类别',
  `names` varchar(50) DEFAULT NULL,
  `values` float(50,3) DEFAULT NULL,
  `number` smallint(10) DEFAULT NULL COMMENT '都可以存放数值，默认values',
  `operate` varchar(100) DEFAULT NULL COMMENT '操作，用于存放大小',
  `remarks` varchar(100) DEFAULT NULL COMMENT '备注',
  `attributes` varchar(50) DEFAULT NULL COMMENT '属性',
  `tips` varchar(100) DEFAULT NULL COMMENT '提示',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_zdsy
-- ----------------------------
DROP TABLE IF EXISTS `t_zdsy`;
CREATE TABLE `t_zdsy` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symc` varchar(100) DEFAULT NULL COMMENT '实验名称',
  `zdlss` int(4) DEFAULT NULL COMMENT '同一批次指导老师数',
  `zdsj` int(4) DEFAULT NULL COMMENT '指导时间',
  `jhxs` int(4) DEFAULT NULL COMMENT '计划学时',
  `xyxs` float(4,1) DEFAULT NULL COMMENT '效益系数',
  `bzxs` float(4,2) DEFAULT NULL COMMENT '标准学时',
  `sjbzxs` float(4,2) DEFAULT NULL COMMENT '实际标准学时',
  `uid` varchar(50) DEFAULT NULL,
  `experimentClassesId` int(11) DEFAULT NULL,
  `experimentId` int(11) DEFAULT NULL,
  `addTimes` datetime DEFAULT NULL COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for t_ztfdk
-- ----------------------------
DROP TABLE IF EXISTS `t_ztfdk`;
CREATE TABLE `t_ztfdk` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `x` float(11,2) DEFAULT NULL,
  `sum` float(11,2) DEFAULT NULL,
  `status` int(10) DEFAULT NULL,
  `uid` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
