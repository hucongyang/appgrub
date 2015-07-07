ALTER TABLE `app_reviews`
DROP COLUMN `Title`,
DROP COLUMN `UserRating`,
DROP COLUMN `AuthorName`,
MODIFY COLUMN `Content`  varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' AFTER `AuthorId`,
MODIFY COLUMN `Status`  tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态 0:正常;-1:删除' AFTER `UpdateTime`;

ALTER TABLE `app_reviews`
ADD COLUMN `ToAuthorId`  int(11) NOT NULL DEFAULT 0 COMMENT '被评论人Id' AFTER `AuthorId`;

ALTER TABLE `notice`
CHANGE COLUMN `replierId` `reviewId`  int(11) NOT NULL DEFAULT 0 COMMENT '关联app_reviews.Id' AFTER `appId`;