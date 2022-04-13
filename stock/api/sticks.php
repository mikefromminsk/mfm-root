<?php

include_once "utils.php";

$ticker = get_required_uppercase(ticker);
$period = get_required_uppercase(period);
$from = get_int(from, time());
$periods = ['1M' => 1 * 60, '5M' => 1 * 60 * 5, '15M' => 1 * 60 * 15, '1H' => 1 * 60 * 60, '1D' => 1 * 60 * 60 * 24];
if ($periods[$period] == null) error("unavailable period");

$response[sticks] = select("select * from sticks where ticker = '$ticker' and period = " . $periods[$period] . " and time <= $from order by time DESC limit 30");

foreach ($response[sticks] as &$stick) {
    $stick[time] = doubleval($stick[time]);
    $stick[low] = doubleval($stick[low]);
    $stick[high] = doubleval($stick[high]);
    $stick[open] = doubleval($stick[open]);
    $stick[close] = doubleval($stick[close]);
    $stick[volume] = doubleval($stick[volume]);
}

echo json_encode_readable($response);