<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/api/auth.php";

$ticker = get_required_uppercase(ticker);
$name = get_required(name);
$description = get_required(description);
$supply = get_int_required(supply);
$price = get_int_required(price);
$starter_supply = get_int_required(starter_supply);
$staking_apy = get_int_required(staking_apy, 10);
$staking_supply = get_int_required(staking_supply, 1000);


$type = IEO;
if ($ticker == USDT) {
    $type = ACTIVE;
    $ieo_user_id = $user_id;
} else {
    $ieo_user_id = createUser(random_key(users, user_id));
}

$tc_user_id = createUser(random_key(users, user_id));
$staking_user_id = createUser(random_key(users, user_id));

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
        tc_user_id => $tc_user_id,
        staking_user_id => $staking_user_id,
        staking_apy => $staking_apy,
    ]);
$ieo_supply = $supply - $staking_supply;
incBalance($ieo_user_id, $ticker, $ieo_supply);
incBalance($staking_user_id, $ticker, $staking_supply);

$response["result"] = place($ieo_user_id, $ticker, 1, $price, $starter_supply) != null;

echo json_encode($response);