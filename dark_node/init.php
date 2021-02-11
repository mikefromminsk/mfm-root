<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

// TODO test user pass

query("DROP TABLE IF EXISTS `domains`;");
query("CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `archived` int(1) DEFAULT 0,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `domain_set_time` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `servers`;");
query("CREATE TABLE IF NOT EXISTS `servers` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `error_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `server_sync_time` double NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


query("DROP TABLE IF EXISTS `data`;");
query("CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `data_parent_id` int(11) DEFAULT NULL,
  `data_key` varchar(64) COLLATE utf8_bin NOT NULL,
  `data_type` int(1) NOT NULL,
  `data_value` varchar(4096) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


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



