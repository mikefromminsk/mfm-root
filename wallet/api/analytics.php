<?php

function trackSum($domain, $key, $value)
{
    $path = implode("/", [analytics, $domain, $key]);
    $timestamp = time();
    foreach (defaultChartSettings() as $period) {
        $period_path = "$path/S$period";
        $period_val = ceil($timestamp / $period) * $period;
        $last_period_val = dataGet([$period_path, time]);
        if ($period_val == $last_period_val) {
            dataInc([$period_path, value], $value, false);
        } else {
            dataSet([$period_path, value], $value, false);
            dataSet([$period_path, history], [
                time => $period_val,
                val => dataGet([$period_path, value])
            ]);
        }
        dataSet([$period_path, time], $period_val, false);
    }
}

function trackValue($domain, $key, $value)
{
    $path = implode("/", [analytics, $domain, $key]);
    $timestamp = time();
    foreach (defaultChartSettings() as $period) {
        $period_path = "$path/S$period";
        $period_val = ceil($timestamp / $period) * $period;
        $last_period_val = dataGet([$period_path, time]);
        if ($period_val == $last_period_val) {
            dataSet([$period_path, low], min(dataGet([$period_path, low]), $value), false);
            dataSet([$period_path, high], max(dataGet([$period_path, high]), $value), false);
            dataSet([$period_path, last], $value, false);
        } else {
            dataSet([$period_path, low], $value, false);
            dataSet([$period_path, high], $value, false);
            dataSet([$period_path, open], $value, false);
            dataSet([$period_path, close], $value, false);
            dataSet([$period_path, history], [
                time => $period_val,
                low => dataGet([$period_path, low]),
                high => dataGet([$period_path, high]),
                open => dataGet([$period_path, open]),
                close => dataGet([$period_path, close])
            ]);
        }
        dataSet([$period_path, time], $period_val, false);
    }
}


function defaultChartSettings()
{
    return [
        60,
        60 * 60,
        60 * 60 * 4,
        60 * 60 * 24,
        60 * 60 * 24 * 7,
        60 * 60 * 24 * 365 * 100,
    ];
}

