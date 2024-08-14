<?php

include_once "utils.php";

$domain = get_required(domain);
$key = get_required(key);
$period_name = get_required(period_name);

$response[candles] = getCandles($domain, $key, $period_name);
$response[value] = getCandleLastValue($domain, $key);

commit($response);