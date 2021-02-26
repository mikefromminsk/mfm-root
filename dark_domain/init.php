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


