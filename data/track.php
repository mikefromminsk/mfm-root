<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

function defaultChartSettings()
{
    return [
        '1M' => 60,
        '1H' => 60 * 60,
        '1D' => 60 * 60 * 24,
        '1Y' => 60 * 60 * 24 * 365 * 100,
    ];
}

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
}


function trackCandles($domain, $key, $value)
{
    $path = implode("/", [analytics, $domain, $key]);
    $timestamp = time();
    foreach (defaultChartSettings() as $period_name => $period) {
        $period_path = "$path/$period_name";

        dataSet([$period_path, close], $value, false);
        dataSet([$period_path, low], min(dataGet([$period_path, low]), $value), false);
        dataSet([$period_path, high], max(dataGet([$period_path, high]), $value), false);

        $period_val = ceil($timestamp / $period) * $period;
        $last_period_val = dataGet([$period_path, time]);
        if ($period_val != $last_period_val) {
            dataSet([$period_path, history], [
                time => $period_val,
                low => dataGet([$period_path, low]),
                high => dataGet([$period_path, high]),
                open => dataGet([$period_path, open]),
                close => $value
            ]);
            dataSet([$period_path, low], $value, false);
            dataSet([$period_path, high], $value, false);
            dataSet([$period_path, open], $value, false);
        }
        dataSet([$period_path, time], $period_val, false);
    }
}

function getCandles($domain, $key, $period_name, $count = 10)
{
    $period = defaultChartSettings()[$period_name];
    if ($period == null) error("unavailable period");

    $time = dataHistory([analytics, $domain, $key, $period_name, history, time]);
    $low = dataHistory([analytics, $domain, $key, $period_name, history, low]);
    $high = dataHistory([analytics, $domain, $key, $period_name, history, high]);
    $open = dataHistory([analytics, $domain, $key, $period_name, history, open]);
    $close = dataHistory([analytics, $domain, $key, $period_name, history, close]);

    $candles = [];

    $candles[] = [
        time => (float)ceil(time() / $period) * $period,
        low => (float)dataGet([analytics, $domain, $key, $period_name, low]),
        high => (float)dataGet([analytics, $domain, $key, $period_name, high]),
        open => (float)dataGet([analytics, $domain, $key, $period_name, open]),
        close => (float)dataGet([analytics, $domain, $key, $period_name, close]),
    ];

    for ($j = 0; $j < sizeof($time); $j++) {
        $candles[] = [
            time => (float)$time[$j],
            low => (float)$low[$j],
            high => (float)$high[$j],
            open => (float)$open[$j],
            close => (float)$close[$j],
        ];
    }
    $candles = array_reverse($candles);
    return $candles;
}

function getCandleLastValue($domain, $key)
{
    return dataGet([analytics, $domain, $key, "1M", close]) ?: 0;
}

function getCandleChange24($domain, $key)
{
    try {
        $period_name = "1D";
        $open = (float)dataGet([analytics, $domain, $key, $period_name, open]);
        $close = (float)dataGet([analytics, $domain, $key, $period_name, close]);
        if ($open == 0) return 0;
        return ($close - $open) / $open  * 100;
    } catch (Exception $e) {
        return 0;
    }
}
