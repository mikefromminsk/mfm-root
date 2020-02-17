<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

query("delete from users");
query("alter table users AUTO_INCREMENT = 1");
query("delete from domains");
query("delete from offers");
query("delete from coins");
query("delete from messages");
query("delete from servers");

$coin_code = get_required("coin_code");
$coin_code = strtoupper($coin_code);

$message = null;

$server_group_id = random_id();

for ($i = 0; $i < 64 && $message == null; $i++) {
    $domains = [];
    for ($j = 0; $j < 1024; $j++) {
        $domain_next_key = random_id();
        $domains[] = array(
            "domain_name" => uencode($coin_code . mb_convert_encoding('&#' . intval($i * 1024 + $j) . ';', 'UTF-8', 'HTML-ENTITIES')),
            "domain_next_key_hash" => hash("sha256", $domain_next_key),
            "domain_next_key" => $domain_next_key,
        );
    }
    $message = http_json_post($server_url . "darknode/domain_set.php", array(
        "domain_name" => $coin_code,
        "domains" => $domains,
        "servers" => [],
    ))["message"];
}

echo json_encode(array("message" => $message));

