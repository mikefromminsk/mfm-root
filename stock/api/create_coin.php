<?php
include_once "auth.php";
include_once "token_utils.php";

$logo = get_required(logo);
$ticker = get_required_uppercase(ticker);
$name = get_required(name);
$description = get_required(description);
$supply = get_int_required(supply);
$price = get_int_required(price);
$starter_supply = get_int_required(starter_supply);
$staking_apy = get_int_required(staking_apy, 10);
$staking_supply = get_int_required(staking_supply, 1000);
$domain = strtolower($name);

if (selectRowWhere(coins, [ticker => $ticker]) != null) error("this ticker exists");

function endsWith($haystack, $needle) {
    return substr_compare($haystack, $needle, -strlen($needle)) === 0;
}

if (is_string($logo)) {
    if (!endsWith($logo, ".svg")) error("need svg");
    file_put_contents("../img/coin/$ticker.svg", file_get_contents($logo));
} else
    if (!move_uploaded_file($logo['tmp_name'], "../img/coin/$ticker.svg")) error("error saving logo");

$type = IEO;
if ($ticker == USDT) {
    $type = ACTIVE;
    $ieo_user_id = $user_id;
} else {
    $ieo_user_id = createUser(random_key(users, user_id));
}

$tc_user_id = createUser(random_key(users, user_id));
$staking_user_id = createUser(random_key(users, user_id));
$drop_user_id = createUser(random_key(users, user_id));
$domain_id = random_key(coins, domain_id, 8);

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
        drop_user_id => $drop_user_id,
        domain => $domain,
        domain_id => $domain_id,
    ]);

$ieo_supply = $supply - $staking_supply;
incBalance($ieo_user_id, $ticker, $ieo_supply);
incBalance($staking_user_id, $ticker, $staking_supply);

generate_token($domain, $supply);
$count = scalar("select count(*) from `tokens` where domain_id = $domain_id");
if ($count != $supply) error("generate tokens error count = $count supply = $supply");
$count = scalar("select count(*) from `keys` where domain_id = $domain_id");
if ($count != $supply) error("generate keys error count = $count supply = $supply");

$response["result"] = place($ieo_user_id, $ticker, 1, $price, $starter_supply) != null;

echo json_encode($response);