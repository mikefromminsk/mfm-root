<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/auth.php";

//sale
$from = get_required("from");
$to = get_required("to");
$price = get_required("price");
$count = get_required("count");

description("create limit request");

/*http_post_json("//dark_wallet/save.php", array(
     "domain_name" => $from,
     "keys" => $keys,
));*/

//sell buy
//creates deals
/*$block = 10;
for ($offset = 0; sizeof($keys) > 0; $offset += $block) {
    $opposite = dataGet("requests.$to", $from, $admin_token, "ask", 0, $block);
    foreach ($opposite as $request) {

    }
    if (sizeof($opposite) < $block)
        break;
}*/

$response["limit"] = dataAdd("requests.$from.$to.$price", $login, $admin_token, $count) ? true : false;

//calc rates
//calc volume


echo json_encode($response);