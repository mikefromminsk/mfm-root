<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

function dataWalletHash($path, $username, $password)
{
    return md5(md5($path . $username . $password));
}

echo json_encode("$host_name") . "\n";

$gas_domain = "data";
$gas_path = "$gas_domain/wallet";

dataWalletInit($gas_path, admin, md5(pass), 100000000.0);
assertEquals("dataWalletInit", dataGet([$gas_path, admin, amount]), 100000000.0);

dataWalletReg($gas_path, user1, md5(pass));
dataWalletSend($gas_path, admin, user1, 2000.0, pass, md5(pass2));
dataWalletSend($gas_path, user1, admin, 1.0, pass, md5(pass2));
assertEquals("dataSend", dataGet([$gas_path, admin, amount]), 100000000.0 - 2000.0 + 1.0);

dataWalletDelegate($gas_path, user1, pass2, "wallet/api/testDelegate.php");

$_POST[gas_address] = admin;
$_POST[gas_key] = pass2;
$_POST[gas_next_hash] = md5(pass3);
commit("commit");
echo "\n";

assertEquals("testDelegate", http_post_json("$host_name/wallet/api/testDelegate.php", [])[success], true);
assertEquals("balanceAfterBurn", dataGet([$gas_path, user1, amount]), 1999 - FILE_ROW_SIZE);

$gas_index = 3;
function gas()
{
    return [
        gas_address => admin,
        gas_key => pass . $GLOBALS[gas_index],
        gas_next_hash => md5(pass . (++$GLOBALS[gas_index])),
    ];
}

function send($domain, $address, $key = null, $hash = null, $amount = 10000, $script = null)
{
    assertEquals("testReg $script", http_post_json($GLOBALS[host_name] . "/$domain/api/token/reg.php", [
            address => $address,
            next_hash => md5(pass),
        ] + gas())[success]);

    if ($key == null) {
        $key = pass . $GLOBALS[gas_index];
        $hash = md5(pass . ++$GLOBALS[gas_index]);
    }

    assertEquals("testSend $script", http_post_json($GLOBALS[host_name] . "/$domain/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => $key,
            next_hash => $hash,
            amount => $amount,
        ] + gas())[success]);

    if ($script != null)
        assertEquals("testDelegate $script", http_post_json($GLOBALS[host_name] . "/$domain/api/token/delegate.php", [
                address => $address,
                password => pass,
                script => $script,
            ] + gas())[success]);
}

send($gas_domain, $gas_domain . "_drop", null, null, 10000, "$gas_domain/api/token/drop.php");
send($gas_domain, $gas_domain . "_reg", null, null, 10000, "$gas_domain/api/token/free_reg.php");

assertEquals("archive gas", http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => "gas",
])[success]);
upload($gas_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
assertNotEquals("upload $gas_domain", sizeof(dataKeys([store, $gas_domain])), 0);

$new_domain = "gas";
$new_path = "$new_domain/wallet";
upload($new_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
dataWalletInit($new_path, admin, md5(pass1), 10000000);
assertEquals("init $new_path", dataGet([$new_path, admin, amount]), 10000000);

$quote_domain = "usdt";
$quote_path = "$quote_domain/wallet";
upload($quote_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
dataWalletInit($quote_path, admin, md5(pass1), 10000000);
assertEquals("init $new_path", dataGet([$new_path, admin, amount]), 10000000);

$sell_amount = 10000;
$sell_price = 3;
$buy_amount = 100;
assertEquals("ico sell", http_post_json($GLOBALS[host_name] . "/$new_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass1,
        next_hash => md5(pass2),
        amount => $sell_amount,
        price => $sell_price,
    ] + gas())[success]);
assertEquals("ico $new_domain amount", dataGet([$new_path, ico, amount]), $sell_amount);
assertEquals("ico $new_domain price", dataGet([$new_domain, price]), $sell_price);


dataWalletReg($quote_path, test_ico_buy, md5(pass1));
dataWalletReg($new_path, test_ico_buy, md5(pass1));
dataWalletSend($quote_path, admin, test_ico_buy, 10000, pass1, md5(pass2));
assertEquals("test_ico_buy usdt amount", dataGet([$quote_path, test_ico_buy, amount]), 10000);
assertEquals("ico buy", http_post_json($GLOBALS[host_name] . "/$new_domain/api/token/ico_buy.php", [
        address => test_ico_buy,
        key => pass1,
        next_hash => md5(pass2),
        amount => $buy_amount,
    ] + gas())[success]);
assertEquals("after buy ico gas balance", dataGet([$new_path, ico, amount]), $sell_amount - $buy_amount);
assertEquals("after buy ico usdt balance", dataGet([$quote_path, test_ico_buy, amount]), $sell_amount - ($buy_amount * $sell_price));

// data for ui tests

dataWalletReg($gas_path, user, dataWalletHash($gas_path, "user", "pass"));
dataWalletSend($gas_path, admin, user, 100000.0, pass . $GLOBALS[gas_index], md5(pass . ++$GLOBALS[gas_index]));

send($gas_domain, $quote_domain . "_drop", null, null, 100000, "$quote_domain/api/token/drop.php");
send($quote_domain, $quote_domain . "_drop", pass2, md5(pass3), 1000000, "$quote_domain/api/token/drop.php");
assertEquals("$quote_domain drop balance", dataGet([$quote_path, $quote_domain . "_drop", amount]), 1000000);

assertEquals("ico $quote_domain", http_post_json($GLOBALS[host_name] . "/$quote_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass3,
        next_hash => md5(pass4),
        amount => 100,
        price => 1,
    ] + gas())[success]);

assertEquals("ico $gas_domain", http_post_json($GLOBALS[host_name] . "/$gas_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass . $GLOBALS[gas_index],
        next_hash => md5(pass . ++$GLOBALS[gas_index]),
        amount => 100000,
        price => 0.1,
    ] + gas())[success]);

echo $gas_index;