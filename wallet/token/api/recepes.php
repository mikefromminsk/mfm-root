<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$response[recipes] = [];

foreach (dataKeys([wallet, recipes]) as $item) {
    $recipe[domain] = $item;
    $recipe[owner] = tokenOwner($item);
    $recipe[price] = getCandleLastValue($item . _price);
    $recipe[price24] = getCandleChange24($item . _price);
    $response[recipes][] = $recipe;
}

commit($response);