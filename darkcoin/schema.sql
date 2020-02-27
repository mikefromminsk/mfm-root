-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 27 2020 г., 14:51
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
  `domain_next_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `domain_next_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `server_group_id` bigint(14) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `file_parent_id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(59) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_data` varchar(59) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message_title` varchar(150) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `message_text` varchar(1000) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `message_type` varchar(256) DEFAULT NULL,
  `message_object_id` bigint(20) DEFAULT NULL,
  `message_readied` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE IF NOT EXISTS `offers` (
  `offer_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `have_coin_code` varchar(5) NOT NULL,
  `have_coin_count` int(11) NOT NULL,
  `want_coin_code` varchar(5) NOT NULL,
  `want_coin_count` int(11) NOT NULL,
  `start_have_coin_count` int(11) NOT NULL,
  `start_want_coin_count` int(11) NOT NULL,
  `offer_rate` double NOT NULL,
  `offer_rate_inverse` double NOT NULL,
  `offer_progress` double NOT NULL,
  `back_script_url` varchar(256) NOT NULL,
  `back_user_login` varchar(64) NOT NULL,
  PRIMARY KEY (`offer_id`)
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

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_login` varchar(64) NOT NULL,
  `user_password_hash` varchar(64) NOT NULL,
  `user_session_token` bigint(14) NOT NULL,
  `user_stock_token` bigint(14) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_session_token` (`user_session_token`),
  UNIQUE KEY `user_login` (`user_login`),
  UNIQUE KEY `user_stock_token` (`user_stock_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
