<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

$address = get_required(address);
$password = get_required(password);

if (!DEBUG) error("cannot use not in debug session");

requestEquals("token/init.php", [
    address => $address,
    password => $password
]);

function installApp($domain, $app_domain)
{
    requestEquals("wallet/store/api/archive.php", [domain => $app_domain]);
    requestEquals("wallet/store/api/install.php", [
        domain => $domain,
        app_domain => $app_domain,
    ]);
}

function launchList($tokens, $address, $password)
{
    foreach ($tokens as $token) {
        $domain = $token[domain];
        $amount = $token[amount] ?: 1000000;
        launch($domain, $address, tokenNextHash($domain, $address, $password), $amount);
        if ($token[craft] != null) {
            $domain1 = array_keys($token[craft])[0];
            $domain2 = array_keys($token[craft])[1];
            installApp($domain, craft);
            // if (!dataExist([$domain, recipe2]))
            recipe2($domain1, $domain2);
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
        domain => "utility_crafting_table",
        craft => ["oak_log" => 1, "stone" => 1]
    ],
];

launchList($tokens, $address, $password);


$response[success] = true;

echo json_encode($response);