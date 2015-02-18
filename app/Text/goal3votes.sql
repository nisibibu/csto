-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015 年 2 朁E18 日 20:40
-- サーバのバージョン： 5.6.20
-- PHP Version: 5.5.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `soccer`
--

-- --------------------------------------------------------

--
-- テーブルの構造 `goal3votes`
--

CREATE TABLE IF NOT EXISTS `goal3votes` (
  `held_time` int(11) DEFAULT NULL,
  `held_date` date DEFAULT NULL,
  `no` int(11) DEFAULT NULL,
  `team` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `position` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `0_vote` float DEFAULT NULL,
  `1_vote` float DEFAULT NULL,
  `2_vote` float DEFAULT NULL,
  `3_vote` float DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `goal3votes`
--

INSERT INTO `goal3votes` (`held_time`, `held_date`, `no`, `team`, `position`, `0_vote`, `1_vote`, `2_vote`, `3_vote`, `created`, `modified`) VALUES
(698, '2014-05-31', 1, '湘南', 'Home', 10.98, 25.48, 36.77, 26.77, '2015-02-18 15:13:29', '2015-02-18 15:13:29'),
(698, '2014-05-31', 2, '東京Ｖ', 'Away', 46.32, 38.72, 10.61, 4.35, '2015-02-18 15:13:29', '2015-02-18 15:13:29'),
(698, '2014-06-01', 3, '鹿島', 'Home', 17.16, 37.2, 31.73, 13.91, '2015-02-18 15:13:29', '2015-02-18 15:13:29'),
(698, '2014-06-01', 4, '清水', 'Away', 23.84, 40.12, 25.16, 10.88, '2015-02-18 15:13:29', '2015-02-18 15:13:29'),
(698, '2014-06-01', 5, '神戸', 'Home', 13.84, 28.21, 34.31, 23.64, '2015-02-18 15:13:29', '2015-02-18 15:13:29'),
(698, '2014-06-01', 6, '仙台', 'Away', 33.81, 41.26, 18.07, 6.86, '2015-02-18 15:13:29', '2015-02-18 15:13:29');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
