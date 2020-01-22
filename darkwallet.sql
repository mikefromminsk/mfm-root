-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Янв 22 2020 г., 09:13
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
  `coin_id` int(11) NOT NULL AUTO_INCREMENT,
  `coin_name` varchar(20) NOT NULL,
  `coin_code` varchar(5) NOT NULL,
  PRIMARY KEY (`coin_id`),
  UNIQUE KEY `coin_name` (`coin_name`),
  UNIQUE KEY `coin_code` (`coin_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `domains`
--

DROP TABLE IF EXISTS `domains`;
CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `domain_next_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `domain_location` varchar(256) CHARACTER SET utf8 DEFAULT NULL,
  `domain_last_active_time` int(11) NOT NULL,
  UNIQUE KEY `name` (`domain_name`),
  KEY `name_2` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `domain_keys`
--

DROP TABLE IF EXISTS `domain_keys`;
CREATE TABLE IF NOT EXISTS `domain_keys` (
  `user_id` int(11) NOT NULL,
  `coin_id` int(11) NOT NULL,
  `domain_name` varchar(256) NOT NULL,
  `domain_next_name` varchar(256) NOT NULL
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
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_session_token` (`user_session_token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
