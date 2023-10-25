<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$path = get_required(path);
$order = get_string(order, desk);
$size = get_int(size, 20);
$page = get_int(page, 1);

$response[history] = selectList("select * from history where data_path = '$path'"
    ." order by action_time $order offset " . (($page - 1) * $size) . " limit $size");

echo json_encode($response);