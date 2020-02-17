<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darknode/domain_utils.php";

$domain_name = get("domain_name");

$fields = "domain_name, domain_prev_key, domain_next_key_hash";
$domain = selectMap("select $fields from domains where domain_name = '" . uencode($domain_name) . "'");
$domain_name_hash = domain_hash($domain_name);
$similar = select("select $fields from domains "
    . " where domain_name_hash > " . ($domain_name_hash - 32768) . " and domain_name_hash < " . ($domain_name_hash + 32768)
    . " order by ABS(domain_name_hash - $domain_name_hash)  limit 5");

$res = array(
    "domain" => $domain,
    "similar" => $similar,
    "servers" => array(
        12 => array("server_url" => "http://localhost/darknode")
    ),
);

echo json_encode_readable($res);