<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/api/auth.php";

$ticker = get_required_uppercase(ticker);
$name = get_required(name);
$description = get_required(description);
$supply = get_int_required(supply);
$price = get_int_required(price);
$starter_supply = get_int_required(starter_supply);


$type = IEO;
if ($ticker == USDT) {
    $type = COIN;
    $ieo_user_id = $user_id;
} else {
    $ieo_user_id = createUser(random_id());
}

insertRow("coins",
    [
        user_id => $user_id,
        created => time(),
        type => $type,
        ticker => $ticker,
        name => $name,
        description => $description,
        supply => $supply,
        ieo_user_id => $ieo_user_id,
    ]);

incBalance($ieo_user_id, $ticker, $supply);

$response["result"] = place($ieo_user_id, $ticker, 1, $price, $starter_supply) != null;

echo json_encode($response);