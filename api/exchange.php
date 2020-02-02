<?php
include_once "login.php";
include_once "domain_utils.php";

$have_coin_code = get_required("have_coin_code");
$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");

$have_coin_code = strtoupper($have_coin_code);
$want_coin_code = strtoupper($want_coin_code);
if ($have_coin_count <= 0)
    db_error(0, "have_coin_count is zero or less zero");
if ($want_coin_count <= 0)
    db_error(0, "want_coin_count is zero or less zero");
$offer_rate = $have_coin_count / $want_coin_count;

$message = null;

$request = array(
    "stock_token" => $user["user_stock_token"],
    "have_coin_code" => $have_coin_code,
    "want_coin_code" => $want_coin_code,
    "back_host_url" => $host_url,
    "back_user_login" => $user["user_login"],
);

$max_request_coin_count = 1024;
$request_count = ceil($have_coin_count / $max_request_coin_count);
for ($i = 0; $i < $request_count && $message == null; $i++) {
    $coin_count = ($i == $request_count - 1) ? bcmod($have_coin_count, $max_request_coin_count) : ($i + 1) * $max_request_coin_count;
    $request["have_coin_count"] = $coin_count;
    $request["want_coin_count"] = ceil($coin_count * $offer_rate);
    $request["have_domain_keys"] = getDomainKeys($user_id, $have_coin_code, $coin_count);
    $message = http_json_post($exchange_host_url . "offer_create.php", $request)["message"];
}

echo json_encode(array(
    "message" => $message,
    "request_count" => $request_count,
    "offer_rate" => $offer_rate,
    "bcmod" => bcmod($have_coin_count, $max_request_coin_count),
));