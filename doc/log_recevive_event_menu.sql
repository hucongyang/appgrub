CREATE TABLE `log_receive_event_menu` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `opt_id` bigint(20) DEFAULT NULL COMMENT '请求标识',
  `userID` bigint(20) NOT NULL COMMENT '对应user.ID',
  `type` varchar(50) NOT NULL COMMENT '事件类型',
  `eventKey` varchar(255) DEFAULT NULL COMMENT '事件场景值',
  `createTime` datetime NOT NULL COMMENT '信息创建时间',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
