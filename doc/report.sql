CREATE TABLE `report_user_active` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `user_all` int(11) DEFAULT '0' COMMENT '用户总数',
  `user_active` int(11) DEFAULT '0' COMMENT '活跃用户数',
  `user_new` int(11) DEFAULT '0' COMMENT '新加入用户',
  PRIMARY KEY (`ID`),
  KEY `date` (`date`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
