<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/login.php";

$from_domain_name = get_required("from_domain_name");
$to_domain_name = get_required("to_domain_name");
$price = get_required("price");
$keys = get_required("keys");

description("create limit request");

http_post_json("//dark_wallet/save.php", array(
     "domain_name" => $from_domain_name,
     "keys" => $keys,
));


//sell buy
//creates deals


$response["success"] = dataPut("requests.$from_domain_name.$to_domain_name.$price",$login, $admin_token, sizeof($keys)) ? true : false;

//calc rates
//calc volume


echo json_encode($response);