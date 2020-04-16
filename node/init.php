<?php

include_once "db.php";

$user = get_required("user");
$pass = get_required("pass");
$master = get_required("master");

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
  `domain_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `error_key_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `server_sync_time` double NOT NULL DEFAULT 0,
  `server_ping` float NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

// cron in self
//http_json_get( "$host_name/node/cron.php?domain_name=node&server_host_name=$master");

/*if ($_SERVER["HTTP_HOST"] == "localhost") {
    http_json_get("localhost/node/commit.php?domain_name=node&domain_key=1&domain_next_key=2");

    http_json_get("host1.com/node/init.php?user=root&pass=root&master=localhost");
}*/

