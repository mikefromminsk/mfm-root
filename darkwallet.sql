-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 11 2020 г., 06:05
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
-- Структура таблицы `coins`
--

DROP TABLE IF EXISTS `coins`;
CREATE TABLE IF NOT EXISTS `coins` (
  `coin_code` varchar(5) NOT NULL,
  `coin_name` varchar(64) NOT NULL,
  UNIQUE KEY `coin_code` (`coin_code`),
  UNIQUE KEY `coin_name` (`coin_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `domains`
--

DROP TABLE IF EXISTS `domains`;
CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `domain_next_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `domain_last_online_time` int(11) NOT NULL,
  `node_location` varchar(256) CHARACTER SET utf8 NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `domain_keys`
--

DROP TABLE IF EXISTS `domain_keys`;
CREATE TABLE IF NOT EXISTS `domain_keys` (
  `user_id` int(11) NOT NULL,
  `domain_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `domain_next_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `coin_code` varchar(5) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`),
  UNIQUE KEY `domain_next_name` (`domain_next_name`)
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
  `back_host_url` varchar(256) NOT NULL,
  `back_user_login` varchar(64) NOT NULL,
  PRIMARY KEY (`offer_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `servers`
--

DROP TABLE IF EXISTS `servers`;
CREATE TABLE IF NOT EXISTS `servers` (
  `server_id` int(11) NOT NULL AUTO_INCREMENT,
  `server_location` varchar(256) NOT NULL,
  `server_sync_time` int(11) NOT NULL,
  `server_success_request_count` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`server_id`),
  UNIQUE KEY `server_location` (`server_location`)
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
  UNIQUE KEY `user_stock_token` (`user_stock_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
