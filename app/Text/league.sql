-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2015 年 2 朁E11 日 15:11
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
-- テーブルの構造 `league`
--

CREATE TABLE IF NOT EXISTS `league` (
  `team` varchar(255) NOT NULL,
  `point` int(11) NOT NULL,
  `v_count` int(11) NOT NULL,
  `d_count` int(11) NOT NULL,
  `l_count` int(11) NOT NULL,
  `goal_point` int(11) NOT NULL,
  `lose_point` int(11) NOT NULL,
  `goal_difference` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `ranking` int(11) NOT NULL,
  `league` varchar(255) CHARACTER SET latin1 NOT NULL,
  `hashtag1` varchar(255) CHARACTER SET latin1 NOT NULL,
  `hashtag2` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- テーブルのデータのダンプ `league`
--

INSERT INTO `league` (`team`, `point`, `v_count`, `d_count`, `l_count`, `goal_point`, `lose_point`, `goal_difference`, `year`, `ranking`, `league`, `hashtag1`, `hashtag2`, `created`, `modified`) VALUES
('ガンバ大阪', 63, 34, 19, 6, 9, 59, 31, 2014, 1, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('浦和レッズ', 62, 34, 18, 8, 8, 52, 32, 2014, 2, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('鹿島アントラーズ', 60, 34, 18, 6, 10, 64, 39, 2014, 3, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('柏レイソル', 60, 34, 17, 9, 8, 48, 40, 2014, 4, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('サガン鳥栖', 60, 34, 19, 3, 12, 41, 33, 2014, 5, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('川崎フロンターレ', 55, 34, 16, 7, 11, 56, 43, 2014, 6, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('横浜F・マリノス', 51, 34, 14, 9, 11, 37, 29, 2014, 7, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('サンフレッチェ広島', 50, 34, 13, 11, 10, 44, 37, 2014, 8, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('FC東京', 48, 34, 12, 12, 10, 47, 33, 2014, 9, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('名古屋グランパス', 48, 34, 13, 9, 12, 47, 48, 2014, 10, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('ヴィッセル神戸', 45, 34, 11, 12, 11, 49, 50, 2014, 11, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('アルビレックス新潟', 44, 34, 12, 8, 14, 30, 36, 2014, 12, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('ヴァンフォーレ甲府', 41, 34, 9, 14, 11, 27, 31, 2014, 13, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('ベガルタ仙台', 38, 34, 9, 11, 14, 35, 50, 2014, 14, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('清水エスパルス', 36, 34, 10, 6, 18, 42, 60, 2014, 15, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('大宮アルディージャ', 35, 34, 9, 8, 17, 44, 60, 2014, 16, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('セレッソ大阪', 31, 34, 7, 10, 17, 36, 48, 2014, 17, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('徳島ヴォルティス', 14, 34, 3, 5, 26, 16, 74, 2014, 18, 'j1', '', '', '2015-02-11 15:09:01', '2015-02-11 15:09:01'),
('湘南ベルマーレ', 101, 42, 31, 8, 3, 86, 25, 2014, 1, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('松本山雅FC', 83, 42, 24, 11, 7, 65, 35, 2014, 2, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('ジェフユナイテッド千葉', 68, 42, 18, 14, 10, 55, 44, 2014, 3, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('ジュビロ磐田', 67, 42, 18, 13, 11, 67, 55, 2014, 4, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('ギラヴァンツ北九州', 65, 42, 18, 11, 13, 50, 50, 2014, 5, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('モンテディオ山形', 64, 42, 18, 10, 14, 57, 44, 2014, 6, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('大分トリニータ', 63, 42, 17, 12, 13, 52, 55, 2014, 7, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('ファジアーノ岡山', 61, 42, 15, 16, 11, 52, 48, 2014, 8, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('京都サンガF.C.', 60, 42, 14, 18, 10, 57, 52, 2014, 9, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('コンサドーレ札幌', 59, 42, 15, 14, 13, 48, 44, 2014, 10, 'j2', '', '', '2015-02-11 15:10:36', '2015-02-11 15:10:36'),
('横浜FC', 55, 42, 14, 13, 15, 49, 47, 2014, 11, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('栃木SC', 55, 42, 15, 10, 17, 52, 58, 2014, 12, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('ロアッソ熊本', 54, 42, 13, 15, 14, 45, 53, 2014, 13, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('V・ファーレン長崎', 52, 42, 12, 16, 14, 45, 42, 2014, 14, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('水戸ホーリーホック', 50, 42, 12, 14, 16, 46, 46, 2014, 15, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('アビスパ福岡', 50, 42, 13, 11, 18, 52, 60, 2014, 16, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('FC岐阜', 49, 42, 13, 10, 19, 54, 61, 2014, 17, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('ザスパクサツ群馬', 49, 42, 14, 7, 21, 45, 54, 2014, 18, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('愛媛FC', 48, 42, 12, 12, 18, 54, 58, 2014, 19, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('東京ヴェルディ', 42, 42, 9, 15, 18, 31, 48, 2014, 20, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('カマタマーレ讃岐', 33, 42, 7, 12, 23, 34, 71, 2014, 21, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37'),
('カターレ富山', 23, 42, 5, 8, 29, 28, 74, 2014, 22, 'j2', '', '', '2015-02-11 15:10:37', '2015-02-11 15:10:37');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;