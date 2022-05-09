<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$drop = get_int("drop", 0);
if ($drop == 1) {
    query("DROP TABLE IF EXISTS `users`;");
    query("CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `token` varchar(128) COLLATE utf32_bin NOT NULL,
    `email` varchar(128) COLLATE utf32_bin NULL,
    PRIMARY KEY (`user_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `balances`;");
    query("CREATE TABLE IF NOT EXISTS `balances` (
    `user_id` int(11) NOT NULL,
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `spot` double NOT NULL,
    `blocked` double NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `coins`;");
    query("CREATE TABLE IF NOT EXISTS `coins` (
  `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
  `name` varchar(25) COLLATE utf32_bin NOT NULL,
  `type` varchar(25) COLLATE utf32_bin NOT NULL,
  `description` varchar(1000) COLLATE utf32_bin NOT NULL,
  `created` int(11) NOT NULL,
  `supply` double NOT NULL,
  `price` double NOT NULL DEFAULT 0,
  `change24` double NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `ieo_user_id` int(11) NOT NULL,
  `tc_user_id` int(11) NOT NULL,
  `staking_user_id` int(11) NOT NULL,
  `staking_apy` int(11) NOT NULL,
  `last_trade_timestamp` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ticker`)
) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `orders`;");
    query("CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_sell` int(11) NOT NULL,
  `status` int(11) DEFAULT 0,
  `price` double NOT NULL,
  `amount` double NOT NULL,
  `filled` double DEFAULT 0,
  `timestamp` int(11) NOT NULL,
   PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `sticks`;");
    query("CREATE TABLE IF NOT EXISTS `sticks` (
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `period` int(11) NOT NULL,
    `time` int(11) NOT NULL,
    `open` double NOT NULL,
    `close` double NOT NULL,
    `high` double NOT NULL,
    `low` double NOT NULL,
    `volume` double NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `tc`;");
    query("CREATE TABLE IF NOT EXISTS `tc` (
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `start` int(11) NOT NULL,
    `finish` int(11) NOT NULL,
    `reward` double NOT NULL,
    `rewarded` double DEFAULT 0
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `trades`;");
    query("CREATE TABLE IF NOT EXISTS `trades` (
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `time` int(11) NOT NULL,
    `maker` int(11) NOT NULL,
    `taker` int(11) NOT NULL,
    `is_sell` int(11) NOT NULL,
    `price` double NOT NULL,
    `amount` double NOT NULL,
    `total` double NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `transfers`;");
    query("CREATE TABLE IF NOT EXISTS `transfers` (
    `transfer_id` int(11) NOT NULL AUTO_INCREMENT,
    `type` varchar(10) COLLATE utf32_bin NULL,
    `from_user_id` int(11) NOT NULL,
    `to_user_id` int(11) NOT NULL,
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `amount` int(11) NOT NULL,
    `parameter` int(11) NULL,
    `time` int(11) NOT NULL,
   PRIMARY KEY (`transfer_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");
} else {
    query("DELETE FROM `users`;");
    query("DELETE FROM `balances`;");
    query("DELETE FROM `coins`;");
    query("DELETE FROM `orders`;");
    query("DELETE FROM `sticks`;");
    query("DELETE FROM `tc`;");
    query("DELETE FROM `trades`;");
    query("DELETE FROM `transfers`;");
}
