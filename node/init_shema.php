<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

$user = get_required("user");
$pass = get_required("pass");

if ($user == $db_user && $pass == $db_pass) {
    query("DROP TABLE IF EXISTS `domains`;");

    query("CREATE TABLE IF NOT EXISTS `domains` (
`domain_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `domain_set_time` int(11) NOT NULL,
  `server_group_id` bigint(14) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `files`;");
    query("CREATE TABLE IF NOT EXISTS `files` (
`file_parent_id` int(11) DEFAULT NULL,
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `file_data` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    query("DROP TABLE IF EXISTS `servers`;");
    query("CREATE TABLE IF NOT EXISTS `servers` (
`server_group_id` bigint(14) NOT NULL,
  `server_url` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_reg_time` int(11) NOT NULL,
  `server_sync_tyme` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    mkdir("files");
}

