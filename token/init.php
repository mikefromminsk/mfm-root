<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

if (!DEBUG) error("cannot use not in debug session");

query("DROP TABLE IF EXISTS `addresses`;");
query("CREATE TABLE IF NOT EXISTS `addresses` (
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `address` varchar(256) COLLATE utf8_bin NOT NULL,
  `prev_key` varchar(256) COLLATE utf8_bin NOT NULL,
  `next_hash` varchar(256) COLLATE utf8_bin NOT NULL,
  `amount` int(11) NOT NULL,
  `delegate` varchar(256) COLLATE utf8_bin DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `trans`;");
query("CREATE TABLE IF NOT EXISTS `trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `from` varchar(256) COLLATE utf8_bin NOT NULL,
  `to` varchar(256) COLLATE utf8_bin NOT NULL,
  `amount` int(11) NOT NULL,    
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


tokenSend($gas_domain, owner, admin, 100000000, ":" . tokenNextHash($gas_domain, admin, pass));

$response[success] = true;

echo json_encode($response);

