<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$drop = get_int("drop", 0);
if ($drop == 1) {
    query("DROP TABLE IF EXISTS `users`;");
    query("CREATE TABLE IF NOT EXISTS `users` (
    `user_id` int(11) NOT NULL AUTO_INCREMENT,
    `token` varchar(128) COLLATE utf32_bin NOT NULL,
    PRIMARY KEY (`user_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `balances`;");
    query("CREATE TABLE IF NOT EXISTS `balances` (
    `user_id` int(11) NOT NULL,
    `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
    `spot` double NOT NULL,
    `blocked` double NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf32 COLLATE=utf32_bin;");

    query("DROP TABLE IF EXISTS `currencies`;");
    query("CREATE TABLE IF NOT EXISTS `currencies` (
  `ticker` varchar(10) COLLATE utf32_bin NOT NULL,
  `rate` double NOT NULL,
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
} else {
    query("DELETE FROM `users`;");
    query("DELETE FROM `balances`;");
    query("DELETE FROM `currencies`;");
    query("DELETE FROM `orders`;");
}