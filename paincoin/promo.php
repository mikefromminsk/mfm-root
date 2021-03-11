a<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$promo = get_required("promo");

description("get keys from promocode");


$response["domain_name"] = "PAIN";

echo json_encode($response);



