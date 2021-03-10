<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/auth.php";

//sale
$give = get_required("give");
$want = get_required("want");
$give_count = get_required("give_count");
$want_count = get_required("want_count");

description("create limit request");


$start_give_count = $give_count;


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

$my_price = $give_count / $want_count;
$opp_price = $want_count / $give_count;

//$response["price"] = $price;
$response["opp_price"] = $opp_price;

$top_prices = dataGet(["requests", $want, $give], $admin_token, $my_price > 1, 0, 2);

foreach ($top_prices as $price_str => $users) {

    $price = floatval($price_str);
    if ($my_price > 1 && $price <= $opp_price || $my_price < 1 && $price >= $opp_price) {
        foreach ($users as $user_login => $request) {
            $exchange_first_count = min($give_count, $request["want"]);
            $exchange_second_count = $exchange_first_count * $price;

            dataDec(["users", $login, $give], $admin_token, $exchange_first_count);
            dataInc(["users", $login, $want], $admin_token, $exchange_second_count);

            dataInc(["users", $user_login, $give], $admin_token, $exchange_first_count);
            dataDec(["users", $user_login, $want], $admin_token, $exchange_second_count);

            if ($request["want"] == $exchange_first_count) {
                dataDelete(["requests", $want, $give, $price_str, $user_login], $admin_token);
                $count = dataCount(["requests", $want, $give, $price_str], $admin_token);
                if ($count == 0)
                    dataDelete(["requests", $want, $give, $price_str], $admin_token);
            }

            $give_count -= $exchange_first_count;
        }
    }
}


$response["satisfied"] = $start_give_count - $give_count;

if ($give_count > 0) {
    $price_str = sprintf("%022.10f", $my_price);
    $request = dataGet(["requests", $give, $want, $price_str, $login], $admin_token);
    if ($request == null)
        $request = array("give" => 0, "want" => 0);
    $request["give"] += $give_count;
    $request["want"] += $want_count;
    $response["push_request"] = dataSet(["requests", $give, $want, $price_str, $login], $admin_token, $request) ? true : false;
}


/*$

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
                ));*/

/*
//calc rates
dataCreate(["rates"], $admin_token);
$last_price = dataGet(["requests", $give, $want], $admin_token, true, 0, 1);
dataSet(["rates", $pair_name], $admin_token, array_keys($last_price)[0]);

//calc volume
dataCreate(["volume"], $admin_token);
$last_volume = dataGet(["volume", $pair_name], $admin_token);
dataInc(["volume", $pair_name], $admin_token, $last_volume);*/


//calc charts


echo json_encode($response);
