<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

$withdrawal_id = get_required(withdrawal_id);
$txid = get_required(txid);

dataSet([usdt, withdrawal, $withdrawal_id, txid], $txid);

$response[success] = true;

commit($response, usdt_withdrawal_success);
