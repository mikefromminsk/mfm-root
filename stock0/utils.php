<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/domains/api/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/properties.php";


function pairName($first, $second)
{
    $arr = [$first, $second];
    sort($arr);
    return join("_", $arr);
}

function pairOpponent($dialog_id, $login)
{
    $arr = explode("_", $dialog_id);
    if ($arr[0] == $arr[1])
        return null;
    if ($arr[0] == $login)
        return $arr[1];
    if ($arr[1] == $login)
        return $arr[0];
    return null;
}

