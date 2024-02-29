<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

// change gas token password

$gas_index = 1;
function gas()
{
    return [
        gas_address => admin,
        gas_key => pass . $GLOBALS[gas_index],
        gas_next_hash => md5(pass . (++$GLOBALS[gas_index])),
    ];
}

$gas_domain = "data";
$gas_path = "$gas_domain/wallet";

assertEquals("data test", http_post($GLOBALS[host_name] . "/$gas_domain/api/test.php", [
    ])[success]);

assertEquals("launch $gas_path", http_post($GLOBALS[host_name] . "/wallet/api/launch.php", [
        domain => $gas_domain,
        address => admin,
        category => L1,
        logo => sha1($gas_domain),
        next_hash => md5(pass1),
        amount => 1000000000,
    ] + gas())[success]);

assertEquals("analytics $gas_domain", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
        path => "analytics/$gas_domain/wallets/S60/value",
    ])[value], 1);

assertEquals("dataWalletInit", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
        path => implode("/", [$gas_path, admin, amount]),
    ])[value], 1000000000);


function send($domain, $address, $key = null, $hash = null, $amount = 10000, $script = null)
{
    assertEquals("testReg $script", http_post($GLOBALS[host_name] . "/$domain/api/token/reg.php", [
            address => $address,
            next_hash => md5(pass),
        ] + gas())[success]);

    if ($key == null) {
        $key = pass . $GLOBALS[gas_index];
        $hash = md5(pass . ++$GLOBALS[gas_index]);
    }

    assertEquals("testSend $script", http_post($GLOBALS[host_name] . "/$domain/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => $key,
            next_hash => $hash,
            amount => $amount,
        ] + gas())[success]);

    if ($script != null)
        assertEquals("testDelegate $script", http_post($GLOBALS[host_name] . "/$domain/api/token/delegate.php", [
                address => $address,
                password => pass,
                script => $script,
            ] + gas())[success]);
}

send($gas_domain, user1, null, null, 10000, "wallet/api/testDelegate.php");

assertEquals("analytics data", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
    path => "analytics/$gas_domain/wallets/S60/value",
])[value], 2);

assertEquals("testDelegate", http_post("$host_name/wallet/api/testDelegate.php", [
])[success]);

//assertEquals("balanceAfterBurn", dataGet([$gas_path, user1, amount]), 1999 - FILE_ROW_SIZE);

send($gas_domain, $gas_domain . "_drop", null, null, 1000000, "$gas_domain/api/token/drop.php");
send($gas_domain, $gas_domain . "_reg", null, null, 10000, "$gas_domain/api/token/free_reg.php");


$new_domain = "drc1";
$new_path = "$new_domain/wallet";
assertEquals("launch $new_path", http_post($GLOBALS[host_name] . "/wallet/api/launch.php", [
        domain => $new_domain,
        address => admin,
        next_hash => md5(pass1),
        logo => sha1($new_domain),
        amount => 10000000,
    ] + gas())[launched], 10000000);

$quote_domain = "usdt";
$quote_path = "$quote_domain/wallet";
assertEquals("launch $quote_path", http_post($GLOBALS[host_name] . "/wallet/api/launch.php", [
        domain => $quote_domain,
        address => admin,
        next_hash => md5(pass1),
        logo => sha1($quote_domain),
        amount => 10000000,
        category => STABLECOIN,
    ] + gas())[launched], 10000000);

$sell_amount = 10000;
$sell_price = 3;
$buy_amount = 100;
assertEquals("ico sell", http_post($GLOBALS[host_name] . "/$new_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass1,
        next_hash => md5(pass2),
        amount => $sell_amount,
        price => $sell_price,
    ] + gas())[success]);

assertEquals("ico $new_domain amount", dataGet([$new_path, ico, amount]), $sell_amount);
assertEquals("ico $new_domain price", dataGet([$new_domain, price]), $sell_price);
assertEquals("analytics ico $gas_domain", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
    path => "analytics/$new_domain/price/S60/open",
])[value], $sell_price);

assertEquals("testReg test_ico_buy", http_post($GLOBALS[host_name] . "/$quote_domain/api/token/reg.php", [
        address => test_ico_buy,
        next_hash => md5(pass1),
    ] + gas())[success]);
assertEquals("testReg test_ico_buy", http_post($GLOBALS[host_name] . "/$new_domain/api/token/reg.php", [
        address => test_ico_buy,
        next_hash => md5(pass1),
    ] + gas())[success]);

assertEquals("testSend admin test_ico_buy", http_post($GLOBALS[host_name] . "/$quote_domain/api/token/send.php", [
        from_address => admin,
        to_address => test_ico_buy,
        password => pass1,
        next_hash => md5(pass2),
        amount => 10000,
    ] + gas())[success]);

assertEquals("test_ico_buy usdt amount", dataGet([$quote_path, test_ico_buy, amount]), 10000);
assertEquals("ico buy", http_post($GLOBALS[host_name] . "/$new_domain/api/token/ico_buy.php", [
        address => test_ico_buy,
        key => pass1,
        next_hash => md5(pass2),
        amount => $buy_amount,
    ] + gas())[success]);

assertEquals("after buy ico gas balance", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
    path => implode("/", [$new_path, ico, amount]),
])[value], $sell_amount - $buy_amount);

assertEquals("after buy ico usdt balance", http_post($GLOBALS[host_name] . "/$gas_domain/api/get.php", [
    path => implode("/", [$quote_path, test_ico_buy, amount]),
])[value], $sell_amount - ($buy_amount * $sell_price));

// data for ui tests


function dataWalletKey($path, $username, $password, $prev_key = "")
{
    return md5($path . $username . $password . $prev_key);
}

function dataWalletHash($path, $username, $password, $prev_key = "")
{
    return md5(dataWalletKey($path, $username, $password, $prev_key));
}

assertEquals("testReg user", http_post($GLOBALS[host_name] . "/$gas_domain/api/token/reg.php", [
        address => user,
        next_hash => dataWalletHash($gas_domain, user, pass),
    ] + gas())[success]);
assertEquals("testSend gas to usdt", http_post($GLOBALS[host_name] . "/$gas_domain/api/token/send.php", [
        from_address => admin,
        to_address => user,
        password => pass . $GLOBALS[gas_index],
        next_hash => md5(pass . ++$GLOBALS[gas_index]),
        amount => 100000.0,
    ] + gas())[success]);


send($gas_domain, $quote_domain . "_drop", null, null, 100000, "$quote_domain/api/token/drop.php");
send($quote_domain, $quote_domain . "_drop", pass2, md5(pass3), 1000000, "$quote_domain/api/token/drop.php");
assertEquals("$quote_domain drop balance", dataGet([$quote_path, $quote_domain . "_drop", amount]), 1000000);

assertEquals("ico $quote_domain", http_post($GLOBALS[host_name] . "/$quote_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass3,
        next_hash => md5(pass4),
        amount => 100,
        price => 1,
    ] + gas())[success]);

assertEquals("ico $gas_domain", http_post($GLOBALS[host_name] . "/$gas_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass . $GLOBALS[gas_index],
        next_hash => md5(pass . ++$GLOBALS[gas_index]),
        amount => 100000,
        price => 0.1,
    ] + gas())[success]);

send($quote_domain, $quote_domain . "_deposits", pass4, md5(pass5), 1000000, "$quote_domain/api/deposit_check.php");
send($gas_domain, "usdt_deposit_check", null, null, 1000000, "$quote_domain/api/deposit_check.php");
send($gas_domain, "usdt_deposit", null, null, 1000000, "$quote_domain/api/deposit_start.php");
send($gas_domain, "usdt_deposit_clear", null, null, 1000000, "$quote_domain/api/clear.php");
send($gas_domain, "wallet_settings", null, null, 1000000, "wallet/api/settings/save.php");
send($gas_domain, "usdt_reg", null, null, 100000, "$quote_domain/api/token/free_reg.php");

assertEquals("testReg user", http_post($GLOBALS[host_name] . "/$quote_domain/api/token/reg.php", [
        address => $quote_domain . "_withdrawals",
        next_hash => dataWalletHash($quote_domain, $quote_domain . "_withdrawals", pass),
    ] + gas())[success]);

send($gas_domain, "usdt_withdrawal_start", null, null, 1000000, "$quote_domain/api/withdrawal_start.php");
send($gas_domain, "usdt_withdrawal_success", null, null, 1000000, "$quote_domain/api/withdrawal_success.php");

send($quote_domain, $quote_domain . "_withdrawal_test", pass5, md5(pass6), 2);

$withdrawal = [
    address => $quote_domain . "_withdrawal_test",
    key => pass,
    nexthash => md5(pass),
    withdrawal_address => test_address,
    amount => 1,
    chain => TRON,
    withdrawal_id => 123,
];

assertEquals("withdrawal_start", http_post( "$GLOBALS[host_name]/$quote_domain/api/withdrawal_start.php",
    $withdrawal)[success]);

$chain = http_post( "$GLOBALS[host_name]/$quote_domain/api/withdrawal_chain.php", []);
assertEquals("withdrawal_chain", sizeof($chain), 1);

$withdrawal[key] = dataWalletKey($quote_domain, $quote_domain . "_withdrawals", pass);
$withdrawal[nexthash] = dataWalletHash($quote_domain, $quote_domain . "_withdrawals", pass, $withdrawal[key]);
assertEquals("withdrawal_success", http_post( "$GLOBALS[host_name]/$quote_domain/api/withdrawal_success.php",
    $withdrawal)[success]);

$withdrawal[key] = pass;
$withdrawal[nexthash] = md5(pass);
$withdrawal[withdrawal_id] = 234;
assertEquals("withdrawal_start2", http_post( "$GLOBALS[host_name]/$quote_domain/api/withdrawal_start.php",
    $withdrawal)[success]);
$chain = http_post( "$GLOBALS[host_name]/$quote_domain/api/withdrawal_chain.php", []);
assertEquals("withdrawal_chain", sizeof($chain), 1);

echo $gas_index;