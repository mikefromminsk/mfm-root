<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$address = get_required(address);
$password = get_required(password);

if (!DEBUG) error("cannot use not in debug session");

function calcPass($domain, $address, $password)
{
    $account = tokenAddress($domain, $address);
    $key = tokenKey($domain, $address, $password, $account[prev_key]);
    $next_hash = tokenNextHash($domain, $address, $password, $key);
    return "$key:$next_hash";
}

function postWithGas($url, $params)
{
    requestEquals($url, array_merge($params, [
        gas_address => $GLOBALS[address],
        gas_pass => calcPass($GLOBALS[gas_domain], $GLOBALS[address], $GLOBALS[password]),
    ]));
}

requestEquals("token/init.php", [
    address => $address,
    password => $password
]);

function installApp($domain, $app_domain)
{
    postWithGas("wallet/store/api/archive.php", [domain => $app_domain]);
    postWithGas("wallet/store/api/install.php", [
        domain => $domain,
        app_domain => $app_domain,
    ]);
}

function launchList($tokens, $address, $password)
{
    foreach ($tokens as $token) {
        $domain = $token[domain];
        $amount = $token[amount] ?: 1000000;
        $recipe = $token[recipe];
        launch($domain, $address, tokenNextHash($domain, $address, $password), $amount);
        postWithGas("world/api/token_deposit.php", [
            domain => $domain,
            amount => tokenAddressBalance($domain, $GLOBALS[address]),
            pass => calcPass($domain, $GLOBALS[address], $GLOBALS[password]),
        ]);
        if ($token[recipe] != null) {
            postWithGas("world/api/recipe_insert.php", [
                domain => $domain,
                recipe => json_encode($recipe),
            ]);
            postWithGas("world/api/send.php", [
                domain => $domain,
                to_address => world,
                amount => $amount,
            ]);
        }
    }
}


$tokens = [
    [domain => "generator_oak_tree"],
    [domain => "generator_rock"],
    [domain => "oak_tree"],
    [domain => "rock"],
    [domain => "oak_log"],
    [domain => "stone"],
    [
        domain => "chest",
        recipe => ["oak_log" => 8]
    ],
];

launchList($tokens, $address, $password);


$response[success] = true;

echo json_encode($response);