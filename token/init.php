<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

if (!DEBUG) error("cannot use not in debug session");

requestEquals("/data/init.php");

$address = get_required(address);
$password = get_required(password);

query("DROP TABLE IF EXISTS `addresses`;");
query("CREATE TABLE IF NOT EXISTS `addresses` (
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `address` varchar(256) COLLATE utf8_bin NOT NULL,
  `prev_key` varchar(256) COLLATE utf8_bin NOT NULL,
  `next_hash` varchar(256) COLLATE utf8_bin NOT NULL,
  `delegate` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `balance` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `trans`;");
query("CREATE TABLE IF NOT EXISTS `trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `from` varchar(256) COLLATE utf8_bin NOT NULL,
  `to` varchar(256) COLLATE utf8_bin NOT NULL,
  `key` varchar(256) COLLATE utf8_bin NOT NULL,
  `next_hash` varchar(256) COLLATE utf8_bin NOT NULL,
  `delegate` varchar(256) COLLATE utf8_bin DEFAULT NULL,
  `amount` float NOT NULL,    
  `time` int(11) NOT NULL,    
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

query("DROP TABLE IF EXISTS `candles`;");
query("CREATE TABLE IF NOT EXISTS `candles` (
  `key` varchar(256) COLLATE utf8_bin NOT NULL,
  `period_name` varchar(2) COLLATE utf8_bin NOT NULL,
  `period_time` int(11) NOT NULL,
  `low` float NOT NULL,
  `high` float NOT NULL,
  `open` float NOT NULL,
  `close` float NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

//https://mytoken.space/token/send.php?domain=usdt&from_address=owner&to_address=admin&amount=100000000&pass=:d7ca392100808830932ba9746fea206f
requestEquals("/token/send.php", [
    domain => $gas_domain,
    from_address => owner,
    to_address => $address,
    amount => 100000000,
    pass => ":" . tokenNextHash($gas_domain, $address, $password)
]);

query("DROP TABLE IF EXISTS `orders`;");
query("CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `address` varchar(256) COLLATE utf8_bin NOT NULL,
  `is_sell` int(1) NOT NULL,
  `status` int(1) DEFAULT 0,
  `price` float NOT NULL,
  `amount` float NOT NULL,
  `filled` float DEFAULT 0,
  `timestamp` int(11) NOT NULL,
   PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");


$response[success] = true;

echo json_encode($response);

