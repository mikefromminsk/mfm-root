a<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$promo = get_required("promo");

description("get keys from promocode");

$response["keys"] = dataGet(["store"], $admin_token, true, 0, 5);

foreach ($response["keys"] as $domain_name => $key)
    dataDelete(["store", $domain_name], $admin_token);

echo json_encode($response);



