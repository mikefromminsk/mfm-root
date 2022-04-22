<?php

include_once "utils.php";

$time = time();
$response[tc] =  select("select * from tc where start <= $time order by start DESC");

echo json_encode($response);