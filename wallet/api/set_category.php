<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$category = get_required(category);

$categories = [
    L1,
    DEFI,
    MEME,
    STABLE,
    AWARDS,
];

if (array_search($category, $category) === false) error("unknown category");
if (dataGet([wallet, info, $domain, owner]) == get_required(gas_address)) error("you are not owner");

$response[success] = dataSet([wallet, info, $domain, category], $category);

commit($response);