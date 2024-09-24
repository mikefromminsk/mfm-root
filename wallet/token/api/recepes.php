<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$response[recipes] = [];

foreach (dataKeys([wallet, recipes], 100) as $domain) {
    $recipe[domain] = $domain;
    $recipe[recipe] = dataObject([$domain, recipe], 100);
    $recipe[owner] = tokenOwner($domain);
    $recipe[price] = getCandleLastValue($domain . _price);
    $recipe[price24] = getCandleChange24($domain . _price);

    $response[recipes][] = $recipe;
}

commit($response);