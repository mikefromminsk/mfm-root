<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$user = get_required("user");
$pass = get_required("pass");

if ($user == $db_user && $pass == $db_pass) {
    query("DROP TABLE IF EXISTS `domains`;");

    query("CREATE TABLE IF NOT EXISTS `domains` (
`domain_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `server_repo_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `domain_set_time` int(11) NOT NULL,
  `server_group_id` bigint(14) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `files`;");
    query("CREATE TABLE IF NOT EXISTS `files` (
  `server_group_id` bigint(14) NOT NULL,
  `file_path` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_level` int(11) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    query("DROP TABLE IF EXISTS `servers`;");
    query("CREATE TABLE IF NOT EXISTS `servers` (
  `server_group_id` bigint(14) NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_repo_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `server_set_time` int(11) NOT NULL,
  `server_sync_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    mkdir("files");
}
