<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

function defaultChartSettings()
{
    return [
        '1M' => 60,
        '1H' => 60 * 60,
        '1D' => 60 * 60 * 24,
        '1W' => 60 * 60 * 24 * 7,
    ];
}
/*
function trackVolume($domain, $key, $value)
{
    $path = implode("/", [analytics, $domain, $key]);
    $timestamp = time();
    foreach (defaultChartSettings() as $period_name => $period) {
        $period_path = "$path/$period_name";
        $period_val = ceil($timestamp / $period) * $period;
        $last_period_val = dataGet([$period_path, time]);
        if ($period_val == $last_period_val) {
            dataInc([$period_path, value], abs($value), false);
        } else {
            dataSet([$period_path, value], abs($value), false);
            dataSet([$period_path, history], [
                time => $period_val,
                val => dataGet([$period_path, value])
            ]);
        }
        dataSet([$period_path, time], $period_val, false);
    }
}

function getChart($domain, $key, $period_name)
{
    $period = defaultChartSettings()[$period_name];
    if ($period == null) error("unavailable period");

    $path = [analytics, $domain, $key, $period_name, history];

    $candles = [];
    $time = dataHistory(array_merge($path, [time]));
    $close = dataHistory(array_merge($path, [close]));
    for ($j = 0; $j < sizeof($time); $j++) {
        $candles[] = [
            time => (float)$time[$j],
            value => (float)$close[$j],
        ];
    }
    $candles = array_reverse($candles);
    return $candles;
}*/


function trackLinear($key, $value)
{
    $timestamp = time();
    foreach (defaultChartSettings() as $period_name => $period) {
        $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '$period_name' "
            . "order by `period_time` desc limit 1");

        $period_time = ceil($timestamp / $period) * $period;
        if ($period_time != $last_candle[period_time]) {
            insertRow(candles, [
                key => $key,
                period_name => $period_name,
                period_time => $period_time,
                low => $value,
                high => $value,
                open => $last_candle[close] ?: $value,
                close => $value
            ]);
        } else {
            updateWhere(candles, [
                low => min($last_candle[low], $value),
                high => max($last_candle[high], $value),
                close => $value
            ], [
                key => $key,
                period_name => $period_name,
                period_time => $period_time
            ]);
        }
    }
}

function trackAccumulate($key, $value = 1)
{
    trackLinear($key, getCandleLastValue($key) + $value);
}

function optimizeCandles($candles)
{
    return array_map(function ($candle) {
        return [
            time => $candle[period_time],
            low => $candle[low],
            high => $candle[high],
            open => $candle[open],
            close => $candle[close],
        ];
    }, $candles);
}

function getCandles($key, $period_name, $count = 10)
{
    $period = defaultChartSettings()[$period_name];
    if ($period == null) error("unavailable period");

    $candles = select("select * from candles where `key` = '$key' and `period_name` = '$period_name' "
        . "order by `period_time` desc limit $count");

    return optimizeCandles(array_reverse($candles));
}

function getCandleLastValue($key)
{
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '1M' "
        . "  order by `period_time` desc limit 1");
    return $last_candle[close];
}

function getCandleChange24($key)
{
    $last_candle = selectRow("select * from candles where `key` = '$key' and `period_name` = '1D' "
        . "order by `period_time` desc limit 1");
    if ($last_candle == null) return 0;
    return $last_candle[close] - $last_candle[open];
}
