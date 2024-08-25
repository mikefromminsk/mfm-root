<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt_bridge/api/utils.php";

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
    echo ("https://apilist.tronscanapi.com/api/new/token_trc20/transfers"
        . "?limit=20&start=0"
        . "&contract_address=$provider[contract]"
        . "&toAddress=$address");
    /*$trans_response = http_get_json("https://apilist.tronscanapi.com/api/new/token_trc20/transfers"
        . "?limit=20&start=0"
        . "&contract_address=$provider[contract]"
        . "&toAddress=$address");
    echo json_encode($trans_response);
    foreach ($trans_response["token_transfers"] as $trans) {
        if ($trans["finalResult"] == "SUCCESS") {
            $response[] = transactionFormat(
                $trans["quant"] / 1000000,
                ceil($trans["block_ts"] / 10000),
                $trans["transaction_id"],
            );
        }
    }*/
    return $response;
}

$response = usdtTrc20Transactions("TPWZ6TNgYBCh18Bf4EVfKesoHHRJ4w8SgT");

commit($response);