<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

if (DEBUG)
    query("DROP TABLE IF EXISTS `data`;");
query("CREATE TABLE IF NOT EXISTS `data` (
  `data_id` int(11) NOT NULL AUTO_INCREMENT,
  `data_parent_id` int(11) DEFAULT NULL,
  `data_key` varchar(64) COLLATE utf8_bin NOT NULL,
  `data_value` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `data_time` int(11) NOT NULL,
  `data_type` int(1) NOT NULL,
  PRIMARY KEY (`data_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

if (DEBUG)
    query("DROP TABLE IF EXISTS `history`;");
query("CREATE TABLE IF NOT EXISTS `history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `data_path` varchar(64) COLLATE utf8_bin NOT NULL,
  `data_value` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `data_time` int(11) NOT NULL,
  `data_type` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

if (DEBUG)
    query("DROP TABLE IF EXISTS `hashes`;");
query("CREATE TABLE IF NOT EXISTS `hashes` (
  `hash` varchar(64) COLLATE utf8_bin NOT NULL,
  `path` varchar(255) COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
