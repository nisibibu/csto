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
-- テーブルの構造 `minivotes`
--

CREATE TABLE IF NOT EXISTS `minivotes` (
  `held_time` int(11) DEFAULT NULL,
  `held_date` date DEFAULT NULL,
  `no` int(11) NOT NULL,
  `home_team` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `away_team` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `1_vote` float DEFAULT NULL,
  `0_vote` float DEFAULT NULL,
  `2_vote` float DEFAULT NULL,
  `class` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- テーブルのデータのダンプ `minivotes`
--

INSERT INTO `minivotes` (`held_time`, `held_date`, `no`, `home_team`, `away_team`, `1_vote`, `0_vote`, `2_vote`, `class`, `created`, `modified`) VALUES
(698, '2014-05-31', 1, '湘南', '東京Ｖ', 15.8, 72.35, 11.85, 'A', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-05-31', 2, '千葉', '愛媛', 25.76, 53.59, 20.65, 'A', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 3, '鹿島', '清水', 19.83, 49.28, 30.89, 'A', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 4, 'Ｆ東京', '鳥栖', 20.3, 35.28, 44.42, 'A', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 5, '神戸', '仙台', 20.31, 57.4, 22.29, 'A', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 1, '浦和', '名古屋', 15.8, 72.35, 11.85, 'B', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 2, '大宮', '新潟', 25.76, 53.59, 20.65, 'B', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-06-01', 3, '柏', '徳島', 19.83, 49.28, 30.89, 'B', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-05-31', 4, '水戸', '松本', 20.3, 35.28, 44.42, 'B', '2015-02-18 20:39:07', '2015-02-18 20:39:07'),
(698, '2014-05-31', 5, '大分', '山形', 20.31, 57.4, 22.29, 'B', '2015-02-18 20:39:07', '2015-02-18 20:39:07');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
