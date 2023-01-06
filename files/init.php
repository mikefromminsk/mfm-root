<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

// TODO test user pass

query("DROP TABLE IF EXISTS `files`;");
query("CREATE TABLE IF NOT EXISTS `files` (
  `path` varchar(64) COLLATE utf8_bin NOT NULL,
  `prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `archived` int(1) DEFAULT 0,
  `data_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `updated` double NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

