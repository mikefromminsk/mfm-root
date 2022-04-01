-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Апр 01 2022 г., 14:49
-- Версия сервера: 5.7.31
-- Версия PHP: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `darknode`
--

-- --------------------------------------------------------

--
-- Структура таблицы `balances`
--

CREATE TABLE IF NOT EXISTS `balances` (
    `user_id` int(11) NOT NULL,
    `balance_type` varchar(16) COLLATE utf32_bin NOT NULL,
    `currency_id` int(11) NOT NULL,
    `balance_spot` double NOT NULL,
    `balance_blocked` double NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `currencies`
--

CREATE TABLE IF NOT EXISTS `currencies` (
    `currency_id` int(11) NOT NULL AUTO_INCREMENT,
    `currency_type` varchar(16) COLLATE utf32_bin NOT NULL,
    `currency_tag` varchar(10) COLLATE utf32_bin NOT NULL,
    `currency_name` varchar(25) COLLATE utf32_bin NOT NULL,
    `currency_logo` varchar(255) COLLATE utf32_bin NOT NULL,
    `currency_rate` double NOT NULL,
    `currency_volume` double NOT NULL,
    `currency_change` double NOT NULL,
    `currency_desc` varchar(4000) COLLATE utf32_bin NOT NULL,
    `currency_supply` int(11) NOT NULL,
    `stake_percent` int(11) NOT NULL,
    `airdrop_count` int(11) NOT NULL,
    `currency_likes` int(11) NOT NULL,
    `domain_name` varchar(255) COLLATE utf32_bin NOT NULL,
    `currency_alert_percent` int(11) NOT NULL,
    PRIMARY KEY (`currency_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
    `message_time` int(11) NOT NULL,
    `message_text` varchar(500) COLLATE utf32_bin NOT NULL,
    `message_type` varchar(16) COLLATE utf32_bin NOT NULL,
    `message_channel` varchar(16) COLLATE utf32_bin NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
    `order_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `order_sell` int(11) NOT NULL,
    `order_price` double NOT NULL,
    `order_amount` double NOT NULL,
    `order_filled` double NOT NULL,
    `order_canceled` int(11) NOT NULL,
    `order_limit` int(11) NOT NULL,
    `order_time` int(11) NOT NULL,
    `currency_id` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `stakes`
--

CREATE TABLE IF NOT EXISTS `stakes` (
    `currency_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `stake_percent` double NOT NULL,
    `stake_time` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `sticks`
--

CREATE TABLE IF NOT EXISTS `sticks` (
    `stick_type` int(11) NOT NULL,
    `stick_time` int(11) NOT NULL,
    `stick_start` double NOT NULL,
    `stick_end` double NOT NULL,
    `stick_max` double NOT NULL,
    `stick_min` double NOT NULL,
    `stick_volume` double NOT NULL,
    `currency_id` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `topics`
--

CREATE TABLE IF NOT EXISTS `topics` (
    `user_id` int(11) NOT NULL,
    `currency_id` int(11) NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `transfers`
--

CREATE TABLE IF NOT EXISTS `transfers` (
    `transfer_time` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `address` varchar(255) COLLATE utf32_bin NOT NULL,
    `transfer_type` varchar(16) COLLATE utf32_bin NOT NULL,
    `currency_id` int(11) NOT NULL,
    `transfer_amount` int(11) NOT NULL,
    `transfer_code` varchar(6) COLLATE utf32_bin NOT NULL,
    `transfer_status` varchar(16) COLLATE utf32_bin NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `user_auth_token` varchar(128) COLLATE utf32_bin NOT NULL,
    PRIMARY KEY (`user_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
