<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$page = get_int(page, 1);
$size = get_int(size, 20);

$ids = dataHistory([$domain, wallet, $address, last_trans], $page, $size);

$trans = [];
foreach ($ids as $id) {
    $tran = [];
    $tran[from] = dataGet([$domain, trans, $id, from]);
    $tran[to] = dataGet([$domain, trans, $id, to]);
    $tran[amount] = dataGet([$domain, trans, $id, amount]);
    $tran[time] = dataInfo([$domain, trans, $id, amount])[data_time];
    // domain
    // order id
    // base amount
    $trans[] = $tran;
}

$response[trans] = $trans;

commit($response);