<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/init.php";


query("DROP TABLE IF EXISTS `payments`;");
query("CREATE TABLE IF NOT EXISTS `payments` (
`payment_id` int(11) NOT NULL AUTO_INCREMENT,
`payment_host` varchar(64) COLLATE utf8_bin NOT NULL,
`payment_currency` varchar(20) COLLATE utf8_bin NOT NULL,
`payment_amount` double NOT NULL,
`domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
`domain_postfix_length` varchar(64) COLLATE utf8_bin NOT NULL,
`payment_create_time` int(11) NOT NULL,
`payment_payment_time` int(11) DEFAULT NULL,
`payment_expire_time` int(11) DEFAULT NULL,
PRIMARY KEY (`payment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");