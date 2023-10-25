<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");
$count = get_required("count");
//$expire_time = get_required("expire_time");

description(basename(__FILE__));

$promo_code = random_id();

$promo_url = "$host_name/promo?code=$promo_code";

dataSet(["users", $login, "promos", $domain_name, $promo_code], $pass, $count);

dataSet(["promos", $promo_code], $pass, array(
     "login" => $login,
     "domain_name" => $domain_name,
     "count" => $count,
     "promo_code" => $promo_code,
     //"expire_time" => $expire_time,
));
$response["promo"] = dataGet(["promos", $promo_code], $pass);

if ($response["promo"] == null) error("promo didnt create");

include_once "promos.php";