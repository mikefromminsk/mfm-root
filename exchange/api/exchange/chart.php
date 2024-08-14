<?php

include_once "utils.php";

$domain = get_required(domain);
$key = get_required(key);
$period_name = get_required(period_name);

$response[chart] = getChart($domain, $key, $period_name);

commit($response);