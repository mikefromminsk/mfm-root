<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$user = get_required("user");
$pass = get_required("pass");

if ($user == $db_user && $pass == $db_pass) {
    query("DROP TABLE IF EXISTS `domains`;");
    query("
CREATE TABLE IF NOT EXISTS `domains` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `domain_set_time` int(11) NOT NULL,
  PRIMARY KEY (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `files`;");
    query("
CREATE TABLE IF NOT EXISTS `files` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `file_path` varchar(256) CHARACTER SET utf8 NOT NULL,
  `file_level` int(11) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8 NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `servers`;");
    query("
CREATE TABLE IF NOT EXISTS `servers` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `server_sync_time` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `keys`;");
    query("
CREATE TABLE IF NOT EXISTS `keys` (
  `domain_name` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `domain_key_hash` varchar(64) NOT NULL,
  `domain_key` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

}
