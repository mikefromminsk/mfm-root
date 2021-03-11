<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$request_id = get_required("request_id");

description("approve");

$promo = random_id();

$request = dataGet(["requests", $request_id], $admin_token);

dataSet(["requests", $request_id, "promo"], $admin_token, $promo);

//domain_set($host_name, );

$promo_url = $host_name . "/dark_wallet/promo.php?login=" .  $request["email"] . "&promo=$promo";

$response["success"] = mail($request["email"], "Вы выиграли paincoun", "For get coins go to follow link: $promo_url");
$response["promo_url"] = $promo_url;

echo json_encode($response);
