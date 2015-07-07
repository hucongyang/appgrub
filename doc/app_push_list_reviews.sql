--
-- 修改时间： 2015-03-26
-- 增加Used字段
--

ALTER TABLE `app_push_list_reviews`
 ADD COLUMN `Used` TINYINT(4) NOT NULL DEFAULT 0 COMMENT '0:未使用；1:已使用';