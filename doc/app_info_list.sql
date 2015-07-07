--
-- 表的结构 `app_info_list`
--

ALTER TABLE `app_info_list`
 ADD COLUMN `PushId` BIGINT(20) DEFAULT '0' COMMENT '关联app_push_list.Id' AFTER `Id`,
 ADD COLUMN `PushStatus` TINYINT(4) NOT NULL DEFAULT '0' COMMENT '1:还没有插入到app_push_list表中；0:已经插入表app_push_list' AFTER `Status`;


--
-- 修改时间： 2015-03-24
-- 删除PushStatus字段，增加VideoUrl字段
--

ALTER TABLE `app_info_list`
 DROP COLUMN `PushStatus`,
 ADD COLUMN `VideoUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP视频链接' AFTER `ScreenShoot`;

--
-- 修改时间： 2015-03-24
-- 增加ShareType字段
--

ALTER TABLE `app_info_list`
 ADD COLUMN `ShareType` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0:用户分享；1：系统分享';

--
-- 修改时间： 2015-03-27
-- 删除字段AppPlatform, AndroidUrl
-- 修改字段IosUrl为AppUrl
--
ALTER TABLE `app_info_list`
 DROP COLUMN `AppPlatform`,
 DROP COLUMN `AndroidUrl`,
 CHANGE COLUMN `IosUrl` `AppUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP爬虫链接';

ALTER TABLE `app_info_list`
DROP COLUMN `Up`;


ALTER TABLE `app_info_list`
ADD COLUMN `Up`  int(11) NOT NULL DEFAULT 0 COMMENT 'App点赞数' AFTER `Rank`;



ALTER TABLE `app_info_list`
MODIFY COLUMN `PushId`  bigint(20) NOT NULL DEFAULT 0 COMMENT '关联app_push_list.Id' AFTER `Id`,
MODIFY COLUMN `AppId`  bigint(20) NOT NULL DEFAULT 0 AFTER `PushId`,
MODIFY COLUMN `SourceId`  int(11) NOT NULL DEFAULT 0 AFTER `AppId`,
MODIFY COLUMN `AppName`  varchar(128) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `SourceId`,
MODIFY COLUMN `MainCategory`  int(11) NOT NULL DEFAULT 0 AFTER `AppName`,
MODIFY COLUMN `CommitUserId`  bigint(20) NOT NULL DEFAULT 0 AFTER `MainCategory`,
MODIFY COLUMN `Remarks`  varchar(1024) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `CommitUserId`,
MODIFY COLUMN `IconUrl`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `Remarks`,
MODIFY COLUMN `AppUrl`  varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'App详情页链接' AFTER `IconUrl`,
MODIFY COLUMN `Rank`  int(11) NOT NULL DEFAULT 0 AFTER `AppUrl`,
MODIFY COLUMN `CommentCount`  int(11) NOT NULL DEFAULT 0 AFTER `Up`,
MODIFY COLUMN `ScreenShoot`  varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `CommentCount`,
MODIFY COLUMN `VideoUrl`  varchar(2048) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'App介绍视频链接' AFTER `ScreenShoot`,
MODIFY COLUMN `UpdateTime`  timestamp NOT NULL AFTER `VideoUrl`,
MODIFY COLUMN `CommitTime`  timestamp NOT NULL COMMENT '分享时间' AFTER `UpdateTime`,
MODIFY COLUMN `Status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '0：正常；1：表示还没有抓取数据；2：抓取失败；' AFTER `OfficialWeb`,
MODIFY COLUMN `MostComment`  int(11) NOT NULL COMMENT '排序：热议' AFTER `Status`,
MODIFY COLUMN `FastUp`  double(11,2) NOT NULL COMMENT '排序：上升最快' AFTER `MostComment`,
MODIFY COLUMN `Sort`  int(11) NOT NULL COMMENT '排序：全部' AFTER `FastUp`,
MODIFY COLUMN `AppInfo`  text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `Sort`,
MODIFY COLUMN `ApkUrl`  varchar(256) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' COMMENT 'android 包下载地址' AFTER `AppInfo`;

UPDATE app_info_list set FastUp = round(FastUp * 1.0 / (DATEDIFF(curdate(), LEFT(UpdateTime, 10)) + 1), 2);