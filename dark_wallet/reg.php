<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";

$login = get_required("login");
$password = get_required("password");
$promo_url = get("promo_url");

description("user registration");


dataCreate(["users"],  $admin_token);
dataCreate(["tokens"],  $admin_token);


$token = random_id();

dataSet(["users", $login], $token, null);
dataSet(["tokens", $token], $admin_token, $login);

$response["token"] = $token;

if ($promo_url != null){
    $promo_response = http_get_json($promo_url);

    $response["promo_added"] = 0;
    foreach ($promo_response["keys"] as $key => $value)
        $response["promo_added"] += dataSet(["users", $login, "wallet", $promo_response["domain_name"], $key], $token, $value) ? 1 : 0;
}

echo  json_encode($response);



