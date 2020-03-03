<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/login.php";

$coin_code = get_required("coin_code");
$coin_code = strtoupper($coin_code);


for ($i = 0; $i < 64; $i++) {
    $domains = [];
    for ($j = 0; $j < 1024; $j++) {
        $domain_key = random_id();
        $domains[] = array(
            "domain_name" => uencode($coin_code . mb_convert_encoding('&#' . intval($i * 1024 + $j) . ';', 'UTF-8', 'HTML-ENTITIES')),
            "domain_key_hash" => hash("sha256", $domain_key),
            "domain_key" => $domain_key,
            "user_login" => $user["user_login"],
        );
    }
    $message = http_json_post($server_url . "darknode/domain_set.php", array(
        "domain_name" => $coin_code,
        "domains" => $domains,
    ))["message"];
    if ($message != null) error($message);
}
if ($user_id != 1){
    $message = http_json_post($server_url . "darkcoin/api/exchange.php", array(
        "token" => scalar("select user_session_token from users where user_id = 1"),
        "have_coin_code" => "USD",
        "have_coin_count" => 1,
        "want_coin_code" => $coin_code,
        "want_coin_count" => 20000,
    ))["message"];
    if ($message != null) error($message);
}

