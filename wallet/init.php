<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

// TODO test user pass

query("DROP TABLE IF EXISTS `keys`;");
query("CREATE TABLE IF NOT EXISTS `keys` (
  `path` varchar(64) COLLATE utf8_bin NOT NULL,
  `key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `key` varchar(64) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

