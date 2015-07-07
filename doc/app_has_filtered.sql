CREATE TABLE `app_has_filtered` (
  `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `Pushid` bigint(20) DEFAULT '0',
  `AppId` bigint(20) DEFAULT '0',
  `SourceId` int(11) DEFAULT '0',
  `AppName` varchar(128) DEFAULT '',
  `MainCategory` int(11) DEFAULT '0',
  `CommitUserId` bigint(20) DEFAULT '0',
  `Remarks` varchar(1024) DEFAULT '',
  `IconUrl` varchar(256) DEFAULT '',
  `IosUrl` varchar(2048) DEFAULT '' COMMENT 'ios mainpage',
  `CommentCount` int(11) DEFAULT '0',
  `ScreenShoot` varchar(2048) DEFAULT '',
  `UpdateTime` timestamp NULL DEFAULT NULL,
  `CommitTime` timestamp NULL DEFAULT NULL COMMENT '分享时间',
  `MoveTime` timestamp NULL DEFAULT NULL COMMENT '从push表挪过来的时间',
  `AppPlatform` int(11) DEFAULT '0' COMMENT '1:ios; 2:andriod',
  `AndroidUrl` varchar(2048) DEFAULT '' COMMENT 'andriod app mainpage',
  `OfficialWeb` varchar(2048) NOT NULL,
  `Status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '1:表示刚从pushbiao表拿过来；0:已经到app_info_list',
  `AppInfo` text,
  `ApkUrl` varchar(256) DEFAULT NULL COMMENT 'android 包下载地址',
  PRIMARY KEY (`Id`),
  KEY `Pushid` (`Pushid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- 修改时间： 2015-03-24
-- 增加VideoUrl字段
--

ALTER TABLE `app_has_filtered`
 ADD COLUMN `VideoUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP视频链接' AFTER `ScreenShoot`;

--
-- 修改时间： 2015-03-25
-- 修改Pushid字段为PushId
--
ALTER TABLE `app_has_filtered`
 CHANGE COLUMN `Pushid` `PushId` bigint(20) DEFAULT '0';

--
-- 修改时间： 2015-03-25
-- 删除字段CommitUserId,CommentCount,UpdateTime,CommitTime
--
ALTER TABLE `app_has_filtered`
 DROP COLUMN `CommitUserId`,
 DROP COLUMN `CommentCount`,
 DROP COLUMN `UpdateTime`,
 DROP COLUMN `CommitTime`;

--
-- 修改时间： 2015-03-27
-- 删除字段Remarks, AppPlatform, AndroidUrl
-- 修改字段IosUrl为AppUrl
--
ALTER TABLE `app_has_filtered`
 DROP COLUMN `Remarks`,
 DROP COLUMN `AppPlatform`,
 DROP COLUMN `AndroidUrl`,
 CHANGE COLUMN `IosUrl` `AppUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP爬虫链接';
