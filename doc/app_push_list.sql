--
-- 修改时间： 2015-03-24
-- 增加VideoUrl字段
--

ALTER TABLE `app_push_list`
 ADD COLUMN `VideoUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP视频链接' AFTER `ScreenShoot`;


--
-- 修改时间： 2015-03-27
-- 删除字段CommitUserId, Remarks, AppPlatform, AndroidUrl
-- 修改字段IosUrl为AppUrl
--
ALTER TABLE `app_push_list`
 DROP COLUMN `CommitUserId`,
 drop COLUMN `Remarks`,
 DROP COLUMN `AppPlatform`,
 DROP COLUMN `AndroidUrl`,
 CHANGE COLUMN `IosUrl` `AppUrl` VARCHAR(2048) DEFAULT '' COMMENT 'APP爬虫链接';

ALTER TABLE `app_push_list` 
MODIFY `FileSize` VARCHAR(20) NULL DEFAULT '' COMMENT '文件大小';

ALTER TABLE `app_push_list`
 ADD COLUMN `PusherId` VARCHAR(256) DEFAULT '' COMMENT 'APP来源市场(source)' AFTER `FileSize`;