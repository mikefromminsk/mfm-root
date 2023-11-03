<?php

include_once "utils.php";

$type = get_required(type);
$value = get_required(value);

switch ($type) {
    case coin_name:
        if (selectRowWhere(coins, [name => $value]) != null) error("Name exists");
        break;
    case coin_ticker:
        if (selectRowWhere(coins, [ticker => strtoupper($value)]) != null) error("Ticker exists");
        break;
}
