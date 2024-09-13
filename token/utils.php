<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/track.php";

$gas_domain = "usdt";


function broadcast($channel, $data)
{
    if (WEB_SOCKETS_ENABLED) {
        http_post("localhost:8002/test", [
            channel => $channel,
            data => $data,
        ]);
    }
}

function tokenKey($domain, $address, $password, $prev_key = "")
{
    return md5($domain . $address . $password . $prev_key);
}

function tokenNextHash($domain, $address, $password, $prev_key = "")
{
    return md5(tokenKey($domain, $address, $password, $prev_key));
}

function tokenFirstTran($domain)
{
    return selectRow("select * from `trans` where `domain` = '$domain' and `from` = 'owner' order by `time` asc limit 1");
}

function tokenOwner($domain)
{
    return tokenFirstTran($domain)[to];
}

function tokenAddress($domain, $address)
{
    return selectRowWhere(addresses, [domain => $domain, address => $address]);
}

function tokenAddressBalance($domain, $address)
{
    $address = tokenAddress($domain, $address);
    if ($address != null) {
        return $address[balance];
    }
    return null;
}

function tokenScriptReg($domain, $address, $script)
{
    if (tokenAddress($domain, $address) == null) {
        return requestEquals("localhost/token/send.php", [
            domain => $domain,
            from_address => owner,
            to_address => $address,
            amount => "0",
            pass => ":",
            script => $script,
            delegate => $script,
        ], success);
    } else {
        return false;
    }
}

function tokenSend(
    $domain,
    $from_address,
    $to_address,
    $amount,
    $pass = ":",
    $delegate = null
)
{
    if ($pass != null) {
        $key = explode(":", $pass)[0];
        $next_hash = explode(":", $pass)[1];
    }
    if ($from_address == owner) {
        if (strlen($domain) < 3 || strlen($domain) > 16) error("domain length has to be between 3 and 16");
        if (tokenAddressBalance($domain, owner) === null) {
            insertRow(addresses, [
                domain => $domain,
                address => owner,
                prev_key => "",
                next_hash => "",
                balance => $amount,
                delegate => "token/send.php",
            ]);
        }
        if (tokenAddressBalance($domain, $to_address) === null) {
            insertRow(addresses, [
                domain => $domain,
                address => $to_address,
                prev_key => "",
                next_hash => $next_hash,
                balance => 0,
                delegate => $delegate,
            ]);
            trackAccumulate($domain . _addresses);
        }
    }

    $from = selectRowWhere(addresses, [domain => $domain, address => $from_address]);
    $to = selectRowWhere(addresses, [domain => $domain, address => $to_address]);
    if ($from[balance] < $amount) error(strtoupper($domain) . " balance is not enough in $from_address wallet");
    if ($to == null) error("$to_address receiver doesn't exist");
    if ($from[delegate] != null) {
        if ($from[delegate] != scriptPath())
            error("script " . scriptPath() . " cannot use $from_address address. Only " . $from[delegate]);
    } else {
        if ($from[next_hash] != md5($key)) error("key is not right");
    }

    if ($from[delegate] != null) {
        updateWhere(addresses, [
            balance => $from[balance] - $amount,
        ], [domain => $domain, address => $from_address]);
    } else {
        updateWhere(addresses, [
            balance => $from[balance] - $amount,
            prev_key => $key,
            next_hash => $next_hash,
        ], [domain => $domain, address => $from_address]);
    }

    updateWhere(addresses, [
        balance => $to[balance] + $amount
    ], [domain => $domain, address => $to_address]);

    $tran = [
        domain => $domain,
        from => $from_address,
        to => $to_address,
        amount => $amount,
        key => $key,
        next_hash => $next_hash,
        time => time(),
    ];

    broadcast(transactions, $tran);

    trackAccumulate($domain . _trans);

    return insertRowAndGetId(trans, $tran);
}

function commit($response = null, $gas_address = null)
{
    if ($response == null)
        $response = [];
    $response[success] = true;
    $gas_rows = 0;
    $gas_rows += count($GLOBALS[new_data]);
    $gas_rows += count($GLOBALS[new_history]);
    $gas_spent = 0.001 * $gas_rows;

    if ($gas_rows != 0) {
        /*if ($gas_address != null) {
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
        }*/
        dataCommit();
        $response[gas_spend] = $gas_spent;
        $response[gas_txid] = $dataTxid;
    }
    echo json_encode($response);
    die();
}


function place($domain, $address, int $is_sell, $price, $amount, $pass = ":")
{
    tokenScriptReg($domain, exchange_ . $domain, "token/place.php");
    tokenScriptReg(usdt, exchange_ . $domain, "token/place.php");

    if ($price !== round($price, 2)) error("price tick is 0.01");
    if ($amount !== round($amount, 2)) error("amount tick is 0.01");
    if ($price <= 0) error("price less than 0");
    if ($amount <= 0) error("amount less than 0");
    $total = round($price * $amount, 4);
    $timestamp = time();

    if ($is_sell == 1) {
        $not_filled = $amount;
        tokenSend($domain, $address, exchange_ . $domain, $amount, $pass);
        foreach (select("select * from orders where `domain` = '$domain' and is_sell = 0 and price >= $price and status = 0 order by price DESC,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $order_filled = $order_not_filled == $coin_to_fill ? 1 : 0;
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_filled], [order_id => $order[order_id]]);
            if ($order_filled == 1) {
                tokenSend($domain, exchange_ . $domain, $order[address], $order[amount]);
            }
            $last_trade_price = $order[price];
            $trade_volume += round($coin_to_fill * $order[price], 4);
            $not_filled = round($not_filled - $coin_to_fill, 2);
            if ($not_filled == 0)
                break;
        }
        if ($not_filled == 0) {
            tokenSend(usdt, exchange_ . $domain, $address, $total);
        }
        $order_id = insertRowAndGetId(orders, [address => $address, domain => $domain, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);
    } else {
        $not_filled = $amount;
        tokenSend(usdt, $address, exchange_ . $domain, $total, $pass);
        foreach (select("select * from orders where `domain` = '$domain' and is_sell = 1 and price <= $price and status = 0 order by price,timestamp") as $order) {
            $order_not_filled = round($order[amount] - $order[filled], 2);
            $coin_to_fill = min($not_filled, $order_not_filled);
            $order_filled = $order_not_filled == $coin_to_fill ? 1 : 0;
            updateWhere(orders, [filled => $order[filled] + $coin_to_fill, status => $order_filled], [order_id => $order[order_id]]);
            if ($order_filled == 1) {
                tokenSend(usdt, exchange_ . $domain, $order[address], round($order[amount] * $order[price], 2));
            }
            $last_trade_price = $order[price];
            $trade_volume += round($coin_to_fill * $order[price], 4);
            $not_filled = round($not_filled - $coin_to_fill, 2);
            if ($not_filled == 0)
                break;
        }
        if ($not_filled == 0) {
            tokenSend($domain, exchange_ . $domain, $address, $amount);
        }
        $order_id = insertRowAndGetId(orders, [address => $address, domain => $domain, is_sell => $is_sell, price => $price, amount => $amount, filled => $amount - $not_filled, status => $not_filled == 0 ? 1 : 0, timestamp => $timestamp]);
    }

    if ($last_trade_price != null) {
        trackAccumulate($domain . _volume, $trade_volume);
        trackLinear($domain . _price, $last_trade_price);

        broadcast(place, [
            domain => $domain,
            price => $last_trade_price,
        ]);
    }

    broadcast(orderbook, [
        domain => $domain,
    ]);

    return $order_id;
}


function placeRange($domain, $min_price, $max_price, $count, $amount_usdt, $is_sell, $address, $pass = ":")
{
    if ($min_price <= 0) error("min_price less than 0");
    if ($max_price <= 0) error("max_price less than 0");
    if ($min_price >= $max_price) error("min_price is greater than max_price");
    if ($count <= 0) error("count less than 0");
    if ($amount_usdt <= 0) error("amount_usdt less than 0");

    if ($amount_usdt < 0.01 * $count) {
        $price = ($is_sell == 1) ? $min_price : $max_price;
        $amount_base = round($amount_usdt / $price, 2);
        if ($amount_base > 0) {
            place($domain, $address, $is_sell, $price, $amount_base, $pass);
        }
    } else {
        $price = $min_price;
        $price_step = round(($max_price - $min_price) / ($count - 1), 2);
        $amount_step = $amount_usdt / $count;

//        echo $amount_usdt . "\n";
//        echo $is_sell . "\n";
//        echo $min_price . "\n";
//        echo $max_price . "\n";
        $sum_amount = 0;
        for ($i = 0; $i < $count; $i++) {
            $price = round($price, 2);
            $amount_base = round($amount_step / $price, 2);
            $sum_amount += ($price * $amount_base);
            if ($i == $count - 1 && $sum_amount < $amount_usdt) {
                while ($sum_amount < $amount_usdt) {
                    $amount_base += 0.01;
                    $sum_amount += $price * 0.01;
                }
            }
            if ($amount_base > 0) {
                //echo $sum_amount . " $price $amount_base\n";
                place($domain, $address, $is_sell, $price, $amount_base, $pass);
            }

            $price += $price_step;
        }
    }
}


function getOrderbook($domain, $count = 6)
{
    function getPriceLevels($domain, $is_sell, $count)
    {
        $levels = select("select price, sum(amount) - sum(filled) as amount from orders "
            . " where `domain` = '$domain' and is_sell = $is_sell and status = 0"
            . " group by price order by price " . ($is_sell == 1 ? ASC : DESC) . " limit $count");
        $sum = array_sum(array_column($levels, amount));
        if ($is_sell == 1)
            $levels = array_reverse($levels);
        $accumulate_amount = 0;
        foreach ($levels as &$level) {
            $accumulate_amount += $level[amount];
            $level[percent] = $accumulate_amount / $sum * 100;
        }
        return $levels;
    }

    $response[sell] = getPriceLevels($domain, 1, $count);
    $response[buy] = getPriceLevels($domain, 0, $count);

    return $response;
}