<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/login.php";

//sale
$from_domain_name = get_required("from_domain_name");
$to_domain_name = get_required("to_domain_name");
$price = get_required("price");
$keys = get_required("keys");

description("create limit request");

$keys_count = sizeof($keys);
$opposite_rate = $keys_count / $price;

http_post_json("//dark_wallet/save.php", array(
     "domain_name" => $from_domain_name,
     "keys" => $keys,
));

//sell buy
//creates deals
$block = 10;
for ($offset = 0; sizeof($keys) > 0; $offset += $block) {
    $opposite = dataGet("requests.$to_domain_name", $from_domain_name, $admin_token, "ask", 0, $block);
    foreach ($opposite as $request) {

    }
    if (sizeof($opposite) < $block)
        break;
}

$response["satisfied"] = $keys_count - sizeof($keys);

if (sizeof($keys))
    $response["public"] = dataPush("requests.$from_domain_name.$to_domain_name.$price",$login, $admin_token, sizeof($keys)) ? true : false;


//calc rates
//calc volume


echo json_encode($response);