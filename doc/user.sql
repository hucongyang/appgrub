--
-- 修改时间： 2015-03-26
-- 增加NickName字段
--

ALTER TABLE `user`
 ADD COLUMN `NickName` varchar(255) DEFAULT '' COMMENT '用户自定义名称' AFTER `UserName`;

UPDATE `user` SET `NickName` = `UserName`;

ALTER TABLE `user` 
MODIFY COLUMN `UserName`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
MODIFY COLUMN `NickName`  varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;


DROP TABLE `reply`;
DROP TABLE `icon`;
DROP TABLE `icon1`;
DROP TABLE `user2`;

ALTER TABLE `appgrub`.`user` ADD COLUMN `IsFollow` TINYINT(3) NULL DEFAULT 0 COMMENT '是否粉丝'  AFTER `Status`;

