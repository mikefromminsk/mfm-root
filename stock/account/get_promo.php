<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$promo_code = get_required("promo_code");

description(basename(__FILE__));

$promo = dataGet(["promos", $promo_code], $pass);

$sender_count = dataGet(["users", $promo["login"], "balance", $promo["domain_name"]], $pass);
$receiver_count = dataGet(["users", $login, "balance", $promo["domain_name"]], $pass);

dataDec(["users", $promo["login"], "balance", $promo["domain_name"]], $pass, $promo["count"]);
dataInc(["users", $login, "balance", $promo["domain_name"]], $pass, $promo["count"]);

$sender_post_count = dataGet(["users", $promo["login"], "balance", $promo["domain_name"]], $pass);
$receiver_post_count = dataGet(["users", $login, "balance", $promo["domain_name"]], $pass);

if ($sender_count - $sender_post_count != $promo["count"] || $receiver_post_count - $receiver_count  != $promo["count"])
    error("receive error");

$response["received"] = $promo["count"];

echo json_encode($response);