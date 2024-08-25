<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt_bridge/api/utils.php";

$deposit_address = get_required(deposit_address);
$chain = get_required(chain);

//if (!dataExist([usdt, deposit, $deposit_address])) error("deposit address is not exist");

$address = dataGet([usdt, deposit, $chain, $deposit_address, address]);
if ($address == null) error("address is null");

//if ($deadline < time()) error("deposit time is finished");

function transactionFormat($amount, $timestamp, $txid)
{
    return [
        "amount" => $amount,
        "block_ts" => $timestamp,
        "transaction_id" => $txid,
    ];
}

function usdtTrc20Transactions($address)
{
    $response = [];
    $provider = PROVIDERS["TRON"];
    $trans_response = http_get_json("https://apilist.tronscanapi.com/api/new/token_trc20/transfers"
        . "?limit=20&start=0"
        . "&contract_address=$provider[contract]"
        . "&toAddress=$address");
    foreach ($trans_response["token_transfers"] as $trans) {
        if ($trans["finalResult"] == "SUCCESS") {
            $response[] = transactionFormat(
                $trans["quant"] / 1000000,
                ceil($trans["block_ts"] / 10000),
                $trans["transaction_id"],
            );
        }
    }
    return $response;
}

function usdtBep20Transactions($address)
{
    $response = [];
    $provider = PROVIDERS["BSC"];
    $trans_response = http_get_json("https://api.bscscan.com/api"
        . "?module=account&action=tokentx&startblock=0&endblock=99999999&page=1&offset=10&sort=asc"
        . "&address=$address"
        . "&contractaddress=$provider[contract]"
        . "&apikey=" . $GLOBALS[bscscan_api]);
    foreach ($trans_response["result"] as $trans) {
        $response[] = transactionFormat(
            $trans["value"] / 1000000000000000000,
            ceil($trans["timeStamp"]),
            $trans["hash"],
        );
    }
    return $response;
}

if ($chain == "TRON"){
    $trans = usdtTrc20Transactions($deposit_address);
} else if ($chain == "BSC") {
    $trans = usdtBep20Transactions($deposit_address);
    $response[trans] = $trans;
}

$deposited = 0;


$last_block_ts = dataGet([usdt, deposit, $chain, $deposit_address, last_block_ts]) ?: 0;
foreach ($trans as $tran) {
    if ($tran[block_ts] > $last_block_ts) {
        $deposited += $tran[amount];
        $last_block_ts = $tran[block_ts];
    }
}

if ($deposited > 0) {
    dataSet([usdt, deposit, $chain, $deposit_address, last_block_ts], $last_block_ts);
    tokenSend(usdt, usdt_deposits, $address, $deposited);
}

$response[last_block_ts] = $last_block_ts;
$response[deposited] = $deposited;

commit($response, usdt_deposit_check);