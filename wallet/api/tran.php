<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$txid = get_required(txid);

$response = getTran($domain, $txid);

commit($response);