<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/utils.php";

$ticker = get_required_uppercase(ticker);
$description = get_required(description);
$distribution = get_required(distribution);

$receiver = dataGet(array_merge($search_text, [wallet]));

commit($response, usdt_check);