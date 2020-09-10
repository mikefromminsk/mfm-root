<?php

include_once "db.php";

//test user pass

query("DROP TABLE IF EXISTS `domains`;");
query("
CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) DEFAULT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `archived` int(1) DEFAULT 0,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `domain_set_time` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `servers`;");
query("
CREATE TABLE IF NOT EXISTS `servers` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `error_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `server_sync_time` double NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


query("DROP TABLE IF EXISTS `actions`;");
query("
CREATE TABLE IF NOT EXISTS `actions` (
`action_id` int(11) NOT NULL AUTO_INCREMENT,
  `action_amount` double NOT NULL,
  `action_currency` varchar(5) COLLATE utf8_bin NOT NULL,
  `action_complete` int(11) NOT NULL,
  `action_datetime` int(11) NOT NULL,
  `user_sender` int(11) NOT NULL,
  `user_receiver` int(11) NOT NULL,
  PRIMARY KEY (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


query("DROP TABLE IF EXISTS `users`;");
query("
CREATE TABLE IF NOT EXISTS `users` (
`user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_email` varchar(64) COLLATE utf8_bin NOT NULL,
  `user_token` varchar(64) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
