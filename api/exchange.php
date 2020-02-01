<?php
include_once "login.php";

$have_coin_code = get_required("have_coin_code");
$have_coin_count = get_int_required("have_coin_count");
$want_coin_code = get_required("want_coin_code");
$want_coin_count = get_int_required("want_coin_count");
$message = null;

$request = array(
    "stock_token" => $user["user_stock_token"],
    "have_coin_code" => $have_coin_code,
    "have_coin_count" => $have_coin_count,
    "want_coin_code" => $want_coin_code,
    "want_coin_count" => $want_coin_count,
    "back_host_url" => $host_url,
    "back_user_login" => $user["user_login"],
);

$max_request_coin_count = 1024;
$request_count = ceil($have_coin_count / $max_request_coin_count);
for ($i = 0; $i < $request_count && $message == null; $i++) {
    $coin_count = $i == $request_count - 1 ? bcmod($have_coin_count, $max_request_coin_count) : $i * $max_request_coin_count;
    $domains_where = " user_id = $user_id and coin_code = '$have_coin_code' limit $coin_count";
    $request["have_domain_keys"] = select("select domain_name, domain_next_name from domain_keys where $domains_where");
    query("delete from domain_keys where $domains_where");
    $message = http_json_post($exchange_server_dir . "offer_create.php", $request)["message"];
}

echo json_encode(array("message" => $message));