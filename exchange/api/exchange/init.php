<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

tokenScriptReg($domain,exchange_ . $domain, $domain . "/api/exchange/place.php");
tokenScriptReg(usdt,exchange_ . $domain, $domain . "/api/exchange/place.php");
tokenScriptReg(usdt,exchange_ . $domain . _gas, $domain . "/api/exchange/place.php");

tokenScriptReg(usdt,exchange_ . $domain . _bot_spred, $domain . "/api/exchange/bot_spred.php");
tokenScriptReg($domain,exchange_ . $domain . _bot_spred, $domain . "/api/exchange/bot_spred.php");

if (DEBUG)
    query("DROP TABLE IF EXISTS `orders`;");
query("CREATE TABLE IF NOT EXISTS `orders` (
  `order_id` int(11) NOT NULL AUTO_INCREMENT,
  `domain` varchar(256) COLLATE utf8_bin NOT NULL,
  `address` varchar(256) COLLATE utf8_bin NOT NULL,
  `is_sell` int(1) NOT NULL,
  `status` int(1) DEFAULT 0,
  `price` double NOT NULL,
  `amount` double NOT NULL,
  `filled` double DEFAULT 0,
  `timestamp` int(11) NOT NULL,
   PRIMARY KEY (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

$response[success] = true;

commit($response);
