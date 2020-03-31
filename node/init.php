<?php

include_once "db.php";

$user = get_required("user");
$pass = get_required("pass");
$domain_name = get("domain_name");
$server_host_name = get("server_host_name");

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

    query("DROP TABLE IF EXISTS `servers`;");
    query("
CREATE TABLE IF NOT EXISTS `servers` (
  `domain_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 NOT NULL,
  `server_repo_hash` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `server_sync_time` int(11) NOT NULL DEFAULT 0,
  `server_ping` float NOT NULL DEFAULT 0,
  `server_reg_time` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


    http_json_get("http://localhost/node/commit.php?domain_key=123&domain_next_key=abc&domain_name=search");
    http_json_get("http://host1.com/node/init.php?domain_key=123&domain_next_key=abc&domain_name=search");

}

// reg new hostname
if ($domain_name != null && $server_host_name != null) {
    if (scalar("select count(*) from servers where domain_name = '" . uencode($domain_name) . "' "
            . " and server_host_name = '" . uencode($server_host_name) . "'") == 0) {
        insertList("servers", array(
            "domain_name" => $domain_name,
            "server_host_name" => $server_host_name,
            "server_reg_time" => time(),
        ));
    }
}
