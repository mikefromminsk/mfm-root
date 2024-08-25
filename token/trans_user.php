<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_string(domain);
$address = get_required(address);
$page = get_int(page, 1);
$size = get_int(size, 10);

$mergedIds = [];

if ($domain != null)
    $userDomains = [$domain];
else
    $userDomains = dataWalletSettingsRead($address, domains);

foreach ($userDomains as $userDomain) {
    $mergedIds[$userDomain] = dataHistory([$userDomain, wallet, $address, last_trans], $page, $size);
}


$trans = [];
foreach ($mergedIds as $userDomain => $ids) {
    foreach ($ids as $id) {
        $trans[] = getTran($userDomain, $id);
    }
}


$response[trans] = $trans;

commit($response);