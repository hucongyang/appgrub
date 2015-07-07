ALTER TABLE `app_info_list`
ADD COLUMN `OfficialWeb`  varchar(2048) NOT NULL AFTER `AndroidUrl`;

ALTER TABLE `app_info_list`
ADD COLUMN `Status`  tinyint NOT NULL DEFAULT 1 COMMENT '1:表示还有抓取数据；' AFTER `OfficialWeb`;




CREATE TABLE `notice` (
  `ID` int(11) DEFAULT NULL,
  `type` int(11) DEFAULT '0' COMMENT '通知类型 0： 系统消息，1：app被评论，2：回复被评论',
  `targetUserid` int(11) DEFAULT '0' COMMENT '收到通知的人ID',
  `msg` varchar(128) DEFAULT NULL,
  `createTime` varchar(128) DEFAULT NULL,
  `sendFlag` tinyint(3) DEFAULT '0' COMMENT '0:未发送\n1：已发送'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


ALTER TABLE `notice`
MODIFY COLUMN `ID`  int(11) NOT NULL AUTO_INCREMENT FIRST ,
MODIFY COLUMN `type`  int(11) NOT NULL DEFAULT 0 COMMENT '通知类型 0： 系统消息，1：app被评论，2：回复被评论' AFTER `ID`,
MODIFY COLUMN `targetUserid`  int(11) NOT NULL DEFAULT 0 COMMENT '收到通知的人ID' AFTER `type`,
MODIFY COLUMN `msg`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `targetUserid`,
MODIFY COLUMN `createTime`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `msg`,
CHANGE COLUMN `sendFlag` `readFlag`  tinyint(3) NOT NULL DEFAULT 1 COMMENT '0:已读\n1：未读' AFTER `createTime`,
ADD PRIMARY KEY (`ID`);



ALTER TABLE `notice`
ADD COLUMN `appId`  int NOT NULL AFTER `readFlag`,
ADD COLUMN `replierId`  int NOT NULL AFTER `appId`;

ALTER TABLE `app_info_list`
CHANGE COLUMN `Score` `Up`  int(11) NULL DEFAULT 0 COMMENT '排序：得分' AFTER `IosUrl`,
ADD COLUMN `MostComment`  int(11) NULL COMMENT '排序：热议' AFTER `Status`,
ADD COLUMN `FastUp`  int(11) NULL COMMENT '排序：上升最快' AFTER `MostComment`,
ADD COLUMN `Sort`  int(11) NULL COMMENT '排序：全部' AFTER `FastUp`;


ALTER TABLE `app_info_list`
ADD COLUMN `AppInfo`  text NULL AFTER `Sort`;

ALTER TABLE `user`
ADD COLUMN `LastLoginTime`  datetime NOT NULL AFTER `Icon`;

ALTER TABLE `appgrub`.`user` ADD COLUMN `Openid` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信ID'  AFTER `PassWord`;

CREATE TABLE `log_api_request` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `opt_id` bigint(20) DEFAULT NULL COMMENT '请求标识',
  `request_time` datetime DEFAULT NULL,
  `request_type` int(11) DEFAULT '0',
  `data` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;


CREATE TABLE `log_api_response` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `opt_id` bigint(20) DEFAULT NULL COMMENT '请求标识',
  `userID` bigint(20) NOT NULL,
  `response_time` datetime DEFAULT NULL,
  `response_type` varchar(20) DEFAULT '0',
  `data` text,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='向用户发送的记录';

CREATE TABLE `log_receive_event` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `opt_id` bigint(20) DEFAULT NULL COMMENT '请求标识',
  `userID` bigint(20) NOT NULL COMMENT '对应user.ID',
  `type` varchar(50) NOT NULL COMMENT '事件类型',
  `eventKey` varchar(255) DEFAULT NULL COMMENT '事件场景值',
  `createTime` datetime NOT NULL COMMENT '信息创建时间',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;

ALTER TABLE `appgrub`.`app_info_list` ADD COLUMN `ApkUrl` VARCHAR(256) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'android 包下载地址'  AFTER `AppInfo`;

ALTER TABLE `appgrub`.`app_info_list` ADD COLUMN `CommitTime` TIMESTAMP NULL DEFAULT NULL COMMENT '分享时间'  AFTER `UpdateTime`;


ALTER TABLE `appgrub`.`user` ADD COLUMN `unionid` VARCHAR(128) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '微信唯一标识'  AFTER `Account`;

DROP TABLE IF EXISTS `favorite`;
CREATE TABLE `favorite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL COMMENT '收藏人ID',
  `appID` int(11) NOT NULL COMMENT '藏品ID',
  `time` datetime NOT NULL DEFAULT '0001-01-01 00:00:00' COMMENT '收藏时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
