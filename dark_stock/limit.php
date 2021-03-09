<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/auth.php";

//sale
$give = get_required("give");
$want = get_required("want");
$give_count = get_required("give_count");
$want_count = get_required("want_count");

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

$pair_name = pairName($give, $want);

$price = $give_count / $want_count;
$opp_price = $want_count / $give_count;


$opp_requests = dataGet(["requests", $want, $give], $admin_token, true, 0, 10);

$request_volume = 0;
foreach ($opp_requests as $req_price => $opp_request) {
    if ($req_price < $opp_price) {
        $response["good_req"][] = $opp_request;
        foreach ($opp_request as $user_login => $opp_give_count) {
            $give_count_to_user = max($give_count, $give_count - $opp_give_count);
            $give_count -= $give_count_to_user;
            dataDec(["users", $user_login, $want], $admin_token, $opp_give_count);
            dataInc(["users", $user_login, $give], $admin_token, $opp_give_count);
            dataDec(["users", $login, $give], $admin_token, $give_count_to_user);
            dataInc(["users", $login, $want], $admin_token, $give_count_to_user);
            $request_volume += $give_count_to_user;
            dataAdd(["deals", $pair_name], $admin_token, array(
                "init" => array(
                    "login" => $login,
                    "give" => $give_count_to_user,
                ),
                "waiter" => array(
                    "login" => $user_login,
                    "have" => $give_count_to_user,
                ),
                "price" => $give_count_to_user,
            ));
        }
    }
}
$price_str = sprintf("%022.10f", $price);
$last_count = dataGet(["requests", $give, $want, $price_str, $login], $admin_token);
$response["limit"] = dataSet(["requests", $give, $want, $price_str, $login], $admin_token, $give_count + $last_count) ? true : false;


//calc rates
dataCreate(["rates"], $admin_token);
$last_price = dataGet(["requests", $give, $want], $admin_token, true, 0, 1);
dataSet(["rates", $pair_name], $admin_token, array_keys($last_price)[0]);

//calc volume
dataCreate(["volume"], $admin_token);
$last_volume = dataGet(["volume", $pair_name], $admin_token);
dataInc(["volume", $pair_name], $admin_token, $last_volume);


//calc charts


$response["o"] = $opp_requests;

echo json_encode($response);