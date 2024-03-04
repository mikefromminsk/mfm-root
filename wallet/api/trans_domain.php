<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$ids = dataHistory([$domain, last_trans]);

$trans = [];
foreach ($ids as $txid) {
    $trans[] = getTran($domain, $txid);
}

$response[trans] = $trans;

commit($response);