<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$gas_domain = "usdt";

function tokenKey($domain, $address, $password, $prev_key = "")
{
    return md5($domain . $address . $password . $prev_key);
}

function tokenNextHash($domain, $address, $password, $prev_key = "")
{
    return md5(tokenKey($domain, $address, $password, $prev_key));
}

function tokenAddressReg($domain, $address, $next_hash, $delegate = null)
{
    if (tokenAddressBalance($domain, $address) !== null) {
        if ($delegate == null)
            error("$domain:$address exist");
    } else {
        insertRow(addresses, [
            domain => $domain,
            address => $address,
            prev_key => "",
            next_hash => $next_hash,
            amount => 0,
            delegate => $delegate,
        ]);
    }
}

function tokenScriptReg($domain, $address, $script)
{
    tokenAddressReg($domain, $address, "", $script);
}

function tokenAddressBalance($domain, $address)
{
    return scalarWhere(addresses, amount, [domain => $domain, address => $address]);
}

function tokenSend(
    $domain,
    $from_address,
    $to_address,
    $amount,
    $pass = null
)
{
    if ($amount == 0) error("amount is 0");
    if ($pass != null) {
        $key = explode(":", $pass)[0];
        $next_hash = explode(":", $pass)[1];
    }
    if ($from_address == owner) {
        if (strlen($domain) < 3 || strlen($domain) > 16) error("domain length has to be between 3 and 16");
        tokenAddressReg($domain, owner, md5($key));
        tokenAddressReg($domain, $to_address, $next_hash);
        updateWhere(addresses, [amount => $amount], [domain => $domain, address => owner]);
    }

    $from = selectRowWhere(addresses, [domain => $domain, address => $from_address]);
    $to = selectRowWhere(addresses, [domain => $domain, address => $to_address]);
    if ($from[amount] < $amount) error(strtoupper($domain) . " balance is not enough in $from_address wallet");
    if ($to == null) error("$to_address receiver doesn't exist");
    if ($key == null && $next_hash == null) {
        if ($from[delegate] != scriptPath())
            error("script " . scriptPath() . " cannot use $from_address address. Only " . dataGet([$domain, token, $from_address, script]));
    } else {
        if ($from[next_hash] != md5($key)) error("key is not right");
    }

    if ($from[delegate] != null) {
        updateWhere(addresses, [
            amount => $from[amount] - $amount,
        ], [domain => $domain, address => $from_address]);
    } else {
        updateWhere(addresses, [
            amount => $from[amount] - $amount,
            prev_key => $key,
            next_hash => $next_hash,
        ], [domain => $domain, address => $from_address]);
    }

    updateWhere(addresses, [
        amount => $to[amount] + $amount
    ], [domain => $domain, address => $to_address]);

    return insertRowAndGetId(trans, [
        domain => $domain,
        from => $from_address,
        to => $to_address,
        amount => $amount,
    ]);
}

function getTran($domain, $txid)
{
    $gas = 0;
    if ($domain != $GLOBALS[gas_domain]) {
        $gasTxid = dataGet([$domain, trans, $txid, gas]);
        $gas = dataGet([$GLOBALS[gas_domain], trans, $gasTxid, amount]);
    }
    return [
        domain => $domain,
        txid => $txid,
        from => dataGet([$domain, trans, $txid, from]),
        to => dataGet([$domain, trans, $txid, to]),
        amount => dataGet([$domain, trans, $txid, amount]),
        time => dataInfo([$domain, trans, $txid])[data_time],
        gas => $gas,
    ];
}

function commit($response, $gas_address = null)
{
    $gas_rows = 0;
    $gas_rows += count($GLOBALS[new_data]);
    $gas_rows += count($GLOBALS[new_history]);
    $gas_spent = 0.001 * $gas_rows;

    if ($gas_rows != 0) {
        if ($gas_address != null) {
            $dataTxid = tokenSend(
                $GLOBALS[gas_domain],
                $gas_address,
                admin,
                $gas_spent);
        } else {
            $dataTxid = tokenSend(
                $GLOBALS[gas_domain],
                get_required(gas_address),
                admin,
                $gas_spent,
                get_required(gas_pass),
            );
        }
        //dataSet([analytics, gas_spent, scriptPath()], $gas_spent, false);
        dataCommit();
        $response[gas_spend] = $gas_spent;
        $response[gas_txid] = $dataTxid;
    }
    echo json_encode($response);
}
