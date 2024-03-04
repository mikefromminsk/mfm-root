<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$page = get_int(page, 1);
$size = get_int(size, 20);
$fromDate = get_int(fromDate);
$toDate = get_int(toDate);

$mergedIds = [];

$userDomains = dataWalletSettingsRead($address, domains);
foreach ($userDomains as $userDomain) {
    if ($fromDate != null && $toDate != null && $fromDate <= $toDate) {
        $ids = dataHistory([$userDomain, wallet, $address, last_trans], 1, 40);
        $filteredIds = [];
        foreach ($ids as $id) {
            $time = dataInfo([$userDomain, trans, $id, amount])[data_time];
            if ($time >= $fromDate && $time <= $toDate) {
                $filteredIds[] = $id;
            }
        }
        $ids = $filteredIds;
    } else {
        $ids = dataHistory([$userDomain, wallet, $address, last_trans], $page, $size);
    }
    $mergedIds[$userDomain] = $ids;
}


$trans = [];
foreach ($mergedIds as $userDomain => $ids) {
    foreach ($ids as $id) {
        $trans[] = getTran($userDomain, $id);
    }
}


$response[trans] = $trans;

commit($response);