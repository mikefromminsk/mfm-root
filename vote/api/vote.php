<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$path = get_required(path);
$address = get_required(address);
$key = get_required(key);
$next_hash = get_required(next_hash);
$value = get_required(value);

$path = explode("/", $path);

$balance = dataWalletBalance($path, [wallet, $address]);
if ($balance > 0) {
    dataInc(array_merge($path, [$value, percent]), $balance);
    if (dataGet(array_merge($path, [answers, $value, percent])) > 0.5){
        dataSet(array_merge($path, [value]), $value);
    }
}

commit($response);