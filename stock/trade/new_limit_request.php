<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

//sale
$pair = get_required("pair");
$give = get_required("give");
$want = get_required("want");
$give_count = get_required("give_count");
$want_count = get_required("want_count");

description(basename(__FILE__));

if ($give_count == 0) error("give_count cannot be 0");
if ($want_count == 0) error("want_count cannot be 0");

if (dataGet(["users", $login, "balance", $give], $pass) < $give_count) error("balance near then give count");

dataDec(["users", $login, "balance", $give], $pass, $give_count);
dataInc(["users", $login, "blocked", $give], $pass, $give_count);

$start_give_count = $give_count;


$pair_name = pairName($give, $want);

$my_rate = $give_count / $want_count;
$opp_rate = $want_count / $give_count;


function deal($pass, $pair, $sender, $give, $give_count, $want, $want_count, $receiver)
{
    dataDec(["users", $sender, "blocked", $give], $pass, $give_count);
    dataInc(["users", $receiver, "balance", $give], $pass, $give_count);

    dataDec(["users", $receiver, "blocked", $want], $pass, $want_count);
    dataInc(["users", $receiver, "balance", $want], $pass, $want_count);

    $time = time();
    $bill_id = random_id();
    dataSet(["bills", $bill_id], $pass, array("count" => $give_count, "whom" => "trade", "comment" => "$bill_id", "time" => $time));
    dataSet(["users", $sender, "bills", $bill_id], $pass, false);
    dataSet(["users", $receiver, "bills", $bill_id], $pass, false);


    $pair = explode("_", $pair);
    $price = $give == $pair[0] ? $give_count / $want_count : $want_count / $give_count;
    $volume_first = $give == $pair[0] ? $give_count : $want_count;
    $volume_second = $give == $pair[0] ? $want_count : $give_count;

    foreach ([1, 15, 60, 1440] as $minutes) {
        $period = $time / 60 / $minutes;
        $data = dataGet(["charts", $pair, $minutes, $period], $pass);
        if ($data == null)
            $data = array("max" => $price, "min" => $price, "start" => $price, "last" => $price, "volume_first" => 0, "volume_second" => 0);
        $data["max"] = max($data["max"], $price);
        $data["min"] = min($data["min"], $price);
        $data["last"] = $price;
        $data["volume_first"] += $volume_first;
        $data["volume_second"] += $volume_second;
        dataSet(["charts", $pair, $minutes, $period], $pass, $data);
    }

    function top($pass, $type, $name, $value, $limit){
        $current = dataMapGet([$type], $pass, $name, $value);
        $min = dataMapInc([$type], $pass);
        $min = dataMapMin([$type], $pass);
    }
}


$top_prices = dataGet(["requests", $want, $give], $pass, false, 0, 2);

foreach ($top_prices as $rate_str => $request_group) {
    $req_rate = floatval($rate_str);
    if ($req_rate >= $opp_rate) {
        foreach ($request_group["req"] as $user_login => $request) {

            $exchange = min($request["want"], $give_count);
            $exchange_second = $exchange * $opp_rate;

            deal($pass, $pair, $login, $give, $exchange, $want, $exchange_second, $user_login);

            dataDec(["requests", $want, $give, $rate_str, "req", $user_login, "give"], $pass, $exchange_second);
            if (dataDec(["requests", $want, $give, $rate_str, "req", $user_login, "want"], $pass, $exchange) <= 0) {
                // push ending to user from ["requests", $want, $give, $rate_str, "req", $user_login, "give"]
                dataDel(["requests", $want, $give, $rate_str, "req", $user_login], $pass);
            }

            dataDec(["requests", $want, $give, $rate_str, $want], $pass, $exchange_second);
            if (dataDec(["requests", $want, $give, $rate_str, $give], $pass, $exchange) <= 0)
                dataDel(["requests", $want, $give, $rate_str], $pass);

            $give_count -= $exchange;
            if ($give_count == 0) break;
        }
    }
    if ($give_count == 0) break;
}

$response["satisfied"] = $start_give_count - $give_count;

if ($give_count > 0) {
    $rate_str = sprintf("%022.10f", $my_rate);
    $opp_rate_str = sprintf("%022.10f", $opp_rate);
    $request = dataGet(["requests", $give, $want, $rate_str, "req", $login], $pass);
    if ($request == null)
        $request = array("give" => 0, "want" => 0, "rate" => $rate_str, "opp_rate" => $opp_rate_str);
    $request["give"] += $give_count;
    $request["want"] += $want_count;
    $response["push_request"] = dataSet(["requests", $give, $want, $rate_str, "req", $login], $pass, $request) ? true : false;
    dataInc(["requests", $give, $want, $rate_str, $give], $pass, $give_count);
    dataInc(["requests", $give, $want, $rate_str, $want], $pass, $want_count);
    dataSet(["requests", $give, $want, $rate_str, "rate"], $pass, $rate_str);
    dataSet(["requests", $give, $want, $rate_str, "opp_rate"], $pass, $opp_rate_str);
}

$pair = pairName($give, $want);

$last_request = dataGet(["requests", $give, $want], $pass, true, -1);
dataSet(["pairs", $pair_name, "rate"], $pass, $last_request["price"]);


/*
//calc volume
$last_volume = dataGet(["volume", $pair_name], $pass);
dataInc(["volume", $pair_name], $pass, $last_volume);*/


//calc charts


echo json_encode_readable($response);
