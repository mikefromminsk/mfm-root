<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/properties.php";


function pairName($first, $second)
{
    $arr = [$first, $second];
    sort($arr);
    return join("_", $arr);
}

