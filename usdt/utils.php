<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

const USDT_OWNER = 'admin';

const USDT_TRC20_CONTRACT = 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t';

const USDT_TRC20_DEPOSIT_ADDRESSES = [
    "TPWZ6TNgYBCh18Bf4EVfKesoHHRJ4w8SgT",
    "TSXvWWCsysLQoPujPCEbYQySXP66ZvN57b",
];

const USDT_TRC20_DEPOSIT_INTERVAL = 30 * 1000;

//https://docs.tronscan.org/api-endpoints/transactions-and-transfers
function usdtTrc20Transactions($wallet_address)
{
    $contract_address = USDT_TRC20_CONTRACT;
    $trans_response = http_get_json("https://apilist.tronscanapi.com/api/new/token_trc20/transfers"
        . "?limit=20&start=0&contract_address=$contract_address&toAddress=$wallet_address");
    $response = [];
    foreach ($trans_response["token_transfers"] as $trans) {
        if ($trans["contract_address"] == $contract_address
            && $trans["to_address"] == $wallet_address
            && $trans["finalResult"] == "SUCCESS") {
            $response[] = [
                "amount" => $trans["quant"] / 1000000,
                "time" => $trans["block_ts"],
                "transaction_id" => $trans["transaction_id"],
                "trans" => $trans
            ];
        }
    }
    return $response;
}

//https://docs.tronscan.org/api-endpoints/account
function usdtTrc20Balance($wallet_address)
{
    $balance_response = http_get_json("https://apilist.tronscanapi.com/api/account/tokens"
        . "?address=$wallet_address&start=0&limit=20&show=2");
    foreach ($balance_response["data"] as $token) {
        if ($token["tokenId"] == USDT_TRC20_CONTRACT) {
            $response["balance"] = $token["quantity"];
        }
    }
    return $response;
}
