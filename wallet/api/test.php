<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

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

dataWalletReg($gas_path, reg, md5(pass));
dataWalletDelegate($gas_path, reg, pass, "data/api/token/free_reg.php");
dataWalletSend($gas_path, admin, reg, 100000.0, pass3, md5(pass4));

dataWalletReg($gas_path, test1, dataWalletHash("data/wallet", "test1", "pass"));
dataWalletSend($gas_path, admin, test1, 200.0, pass4, md5(pass5));

dataWalletReg($gas_path, user, dataWalletHash("data/wallet", "user", "pass"));
dataWalletSend($gas_path, admin, user, 100000.0, pass5, md5(pass6));

$gas_index = 6;
function gas()
{
    return [
        gas_address => admin,
        gas_key => pass . $GLOBALS[gas_index],
        gas_next_hash => md5(pass . (++$GLOBALS[gas_index])),
    ];
}

function sendGas($address, $script = null)
{
    $gas_domain = $GLOBALS[gas_domain];
    assertEquals("testReg $script", http_post_json($GLOBALS[host_name] . "/$gas_domain/api/token/free_reg.php", [
        address => $address,
        next_hash => md5(pass1),
    ])[success]);

    assertEquals("testSend $script", http_post_json($GLOBALS[host_name] . "/$gas_domain/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => pass . $GLOBALS[gas_index],
            next_hash => md5(pass . ++$GLOBALS[gas_index]),
            amount => 100000,
        ] + gas())[success]);

    if ($script != null)
        assertEquals("testDelegate $script", http_post_json($GLOBALS[host_name] . "/$gas_domain/api/token/delegate.php", [
                address => $address,
                password => pass1,
                script => $script,
            ] + gas())[success]);
}

sendGas(gas_giveaway, "$gas_domain/api/token/drop");


assertEquals("archive $gas_domain", http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => $gas_domain,
])[success], true);
upload($gas_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/$gas_domain.zip");
assertNotEquals("upload data", sizeof(dataKeys([store, "data"])), 0);


$new_domain = "gas";
$new_path = "$new_domain/wallet";

assertEquals("archive $new_domain", http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => $new_domain,
])[success], true);
upload($new_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/$new_domain.zip");
assertNotEquals("upload $new_domain", sizeof(dataKeys([store, $new_domain])), 0);
dataWalletInit($new_path, admin, md5(pass), 10000000);


$quote_domain = "usdt";
$quote_path = "$quote_domain/wallet";

upload($quote_domain, $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
assertNotEquals("upload $quote_domain", sizeof(dataKeys([store, $quote_domain])), 0);
dataWalletInit($quote_path, admin, md5(pass), 10000000);


$sell_amount = 10000;
$sell_price = 3;
$buy_amount = 100;
assertEquals("ico sell", http_post_json($GLOBALS[host_name] . "/$new_domain/api/token/ico_sell.php", [
        address => admin,
        key => pass,
        next_hash => md5(pass2),
        amount => $sell_amount,
        price => $sell_price,
    ] + gas())[success], true);

assertEquals("ico sell balance owner", dataGet([$new_path, ico, amount]), $sell_amount);
assertEquals("ico price", dataGet([$new_domain, price]), 3);


sendGas(test_ico_buy);
dataWalletReg($quote_path, test_ico_buy, md5(pass));
dataWalletReg($new_path, test_ico_buy, md5(pass));
dataWalletSend($quote_path, admin, test_ico_buy, 10000, pass, md5(pass1));
assertEquals("test_ico_buy usdt amount", dataGet([$quote_path, test_ico_buy, amount]), 10000);

assertEquals("ico buy", http_post_json($GLOBALS[host_name] . "/$new_domain/api/token/ico_buy.php", [
        address => test_ico_buy,
        key => pass,
        next_hash => md5(pass2),
        amount => $buy_amount,
    ] + gas())[success]);

assertEquals("after buy ico gas balance", dataGet([$new_path, ico, amount]), $sell_amount - $buy_amount);
assertEquals("after buy ico usdt balance", dataGet([$quote_path, test_ico_buy, amount]), $sell_amount - ($buy_amount * $sell_price));

echo $gas_index;