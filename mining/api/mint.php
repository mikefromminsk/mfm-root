<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);
$nonce = get_required(nonce);

$domain = getDomain();

$last_hash = dataGet([$domain, mining, last_hash]) ?: "";
$difficulty = dataGet([$domain, mining, difficulty]) ?: 1;
if (substr(md5($last_hash . $domain . $nonce),0, $difficulty) == str_repeat("0", $difficulty)) {
    $reward = dataWalletBalance($domain, $domain . "_mining") * 0.001;
    $reward = round($reward, 2);
    dataWalletSend($domain, $domain . "_mining", $address, $reward);
    //$difficulty = dataInfo([$domain, mining, last_hash])[data_time];
} else {
    error("Invalid nonce");
}

$response[minted] = $reward;

commit($response);