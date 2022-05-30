<?php

include_once "utils.php";

$ticker = get_required_uppercase(ticker);
$period = get_required_uppercase(period);
$count = 80;
$periods = ['1S' => 1, '1M' => 1 * 60, '5M' => 1 * 60 * 5, '15M' => 1 * 60 * 15, '1H' => 1 * 60 * 60, '1D' => 1 * 60 * 60 * 24];
if ($periods[$period] == null) error("unavailable period");

$period = $periods[$period];
$time = time();

$candles = select("select * from candles where ticker = '$ticker' and period = $period order by time desc limit $count");
$candlesMap = array_to_map($candles, "time");

$last_price = selectRowWhere(coins, [ticker => $ticker])[price];
$result_candles = [];
for ($i = 0; $i < $count; $i++) {
    $time_period = floor($time / $period) * $period - ($period * $i);
    if ($candlesMap[$time_period] != null) {
        $candle = array_shift($candles);
        $last_price = $candle[open];
        $candle[time] = $time_period;
        $result_candles[] = $candle;
    } else {
        $result_candles[] = [
            time => $time_period,
            low => $last_price,
            high => $last_price,
            open => $last_price,
            close => $last_price,
            volume => 0];
    }
}
$response[candles] = array_reverse($result_candles);

echo json_encode_readable($response);