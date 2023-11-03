<?php

include_once "auth.php";

$event_name = get_required(e);
$time = get_int_required(t);
$param1 = get_string(p1);
$param2 = get_string(p2);
$param3 = get_string(p3);
$param4 = get_string(p4);
$param5 = get_string(p5);

insertRow(tracker, [
    user_id => $user_id,
    event_name => $event_name,
    time => $time,
    par1 => $param1,
    par2 => $param2,
    par3 => $param3,
    par4 => $param4,
    par5 => $param5,
]);