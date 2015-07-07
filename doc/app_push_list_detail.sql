-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015-03-17 08:14:26
-- 服务器版本： 5.6.21
-- PHP Version: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `appgrub`
--

-- --------------------------------------------------------

--
-- 表的结构 `app_push_list_detail`
--
-- 创建时间： 2015-03-17 01:42:04
--

CREATE TABLE IF NOT EXISTS `app_push_list_detail` (
`Id` bigint(20) unsigned NOT NULL,
  `AppId` bigint(20) DEFAULT '0',
  `ApkName` varchar(100) DEFAULT NULL,
  `DownLoadNum` int(8) DEFAULT NULL COMMENT '下载数量',
  `CommentNum` int(8) DEFAULT NULL COMMENT '评论数量',
  `Date` date DEFAULT NULL,
  `PushId` bigint(20) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=136 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_push_list_detail`
--
ALTER TABLE `app_push_list_detail`
 ADD PRIMARY KEY (`Id`), ADD UNIQUE KEY `push_detail` (`AppId`,`ApkName`,`Date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_push_list_detail`
--
ALTER TABLE `app_push_list_detail`
MODIFY `Id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=136;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;


ALTER TABLE `app_push_list_detail` 
MODIFY `CommentNum` int(8) NOT NULL DEFAULT 0 COMMENT '评论数量',
MODIFY `DownLoadNum` int(15) NOT NULL DEFAULT 0 COMMENT '下载数量';

ALTER TABLE  `app_push_list_detail` MODIFY `Score` TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'app增量得分';