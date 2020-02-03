<?php

include_once "login.php";
include_once "domain_utils.php";

$coin_name = get_required("coin_name");
$coin_code = get_required("coin_code");
$coin_code = strtoupper($coin_code);
$message = null;

function unichr($u)
{
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

$message = insertList("coins", array(
    "coin_name" => $coin_name,
    "coin_code" => $coin_code,
)) == true ? null : "coin name exist";

if ($message == null) {
    for ($i = 0; $i < 64; $i++) {
        $domain_last_online_time = time();
        $insert_domain_keys_sql = "insert into domain_keys (user_id, coin_code, domain_name, domain_next_name) VALUES ";
        $insert_domains_sql = "insert into domains (domain_name, domain_next_hash, domain_last_online_time, node_location) VALUES ";
        for ($j = 0; $j < 1024; $j++) {
            $domain_name = uencode($coin_name . unichr($i * 1024 + $j));
            $domain_next_name = "" . random_id();
            $domain_next_hash = hash("sha256", $domain_next_name);
            $insert_domain_keys_sql .= "($user_id,'$coin_code','$domain_name','$domain_next_name')" . ($j != 1023 ? "," : "");
            $insert_domains_sql .= "('$domain_name','$domain_next_hash',$domain_last_online_time, '$node_url')" . ($j != 1023 ? "," : "");
        }
        $message = $message == null && query($insert_domain_keys_sql) ? null : "insert_domain_keys_sql error";
        $message = $message == null && query($insert_domains_sql) ? null : "insert_domain_keys_sql error";
    }

    // send 1 coin and 50 usd to exchange server

    syncDomains($coin_code);
}

echo json_encode(array("message" => $message));

