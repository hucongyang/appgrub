ALTER TABLE `notice`
MODIFY COLUMN `type`  int(11) NOT NULL DEFAULT 0 COMMENT '通知类型 0： 系统消息，1：app被评论，2：回复被评论,3:被@' AFTER `ID`;

ALTER TABLE `notice`
MODIFY COLUMN `type`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '通知类型 0： 系统消息，1：app被评论或回复，2：被@' AFTER `ID`,
MODIFY COLUMN `createTime`  datetime NOT NULL AFTER `msg`;

ALTER TABLE `notice`
MODIFY COLUMN `type`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '通知类型 0： 系统消息，1：app被评论，2：回复，3：被@' AFTER `ID`;


ALTER TABLE `appgrub`.`notice` CHANGE COLUMN `msg` `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;