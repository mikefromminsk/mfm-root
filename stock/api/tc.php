<?php

include_once "utils.php";

$time = time();
$response[tc] =  array_to_map(select("select * from tc where start <= $time order by start DESC"), ticker);

echo json_encode($response);