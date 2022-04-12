<?php

include_once "utils.php";

$count = 6; // get_int(count, 6);
$ticker = get_required_uppercase(ticker);

$response["coin"] = selectRowWhere(orders, [ticker => $ticker]);
$response["sell"] = select("select * from orders where ticker = '$ticker' and is_sell = 0 and status = 0 order by price DESC,timestamp limit $count");
$response["buy"] = select("select * from orders where ticker = '$ticker' and is_sell = 1 and status = 0 order by price,timestamp limit $count");

echo json_encode($response);