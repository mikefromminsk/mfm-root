-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Мар 05 2020 г., 10:08
-- Версия сервера: 10.4.10-MariaDB
-- Версия PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `darkwallet`
--

-- --------------------------------------------------------

--
-- Структура таблицы `domains`
--

DROP TABLE IF EXISTS `domains`;
CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `server_group_id` bigint(14) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `file_parent_id` int(11) DEFAULT NULL,
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `file_data` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `servers`
--

DROP TABLE IF EXISTS `servers`;
CREATE TABLE IF NOT EXISTS `servers` (
  `server_group_id` bigint(14) NOT NULL,
  `server_url` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_domain_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
