<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$fromAddress = get_required(fromAddress);
$domain_name = get_required(domain_name);
$ip = get_required(ip);


requestEquals("send.php", [
    fromAddress => $fromAddress,
    toAddress => "admin",
    amount => 1,
], result, 1);


$response["result"] = dataMapSet([dns, domains],
    "pass",
    $domain_name,
    json_encode([ip => $ip]),
) != null;

echo json_encode($response);