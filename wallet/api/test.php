<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

dataWalletInit([data, wallet], admin, md5(password), 100000000.0);
assertEquals("dataWalletInit", dataGet([data, wallet, admin, amount]), 100000000.0);

dataWalletReg([data, wallet], user1, md5(password));
dataWalletSend([data, wallet], admin, user1, 2000.0, password, md5(password2));
dataWalletSend([data, wallet], user1, admin, 1.0, password, md5(password2));
assertEquals("dataSend", dataGet([data, wallet, admin, amount]), 100000000.0 - 2000.0 + 1.0);

dataWalletDelegate([data, wallet], user1, password2, "wallet/api/testDelegate");

$_POST["gas_address"] = admin;
$_POST["gas_key"] = password2;
$_POST["gas_next_hash"] = md5(password3);
commit("commit");

assertEquals("testDelegate", http_post_json("localhost/wallet/api/testDelegate.php", [])[success], true);
assertEquals("balanceAfterBurn", strval(dataGet([data, wallet, user1, amount])), strval(1999 - FILE_ROW_SIZE));

dataWalletReg([data, wallet], reg, md5(password));
dataWalletDelegate([data, wallet], reg, password, "wallet/api/reg");
dataWalletSend([data, wallet], admin, reg, 100000.0, password3, md5(password4));

dataWalletReg([data, wallet], test1, dataWalletHash("data/wallet", "test1", "password"));
dataWalletSend([data, wallet], admin, test1, 200.0, password4, md5(password5));

dataWalletReg([data, wallet], user, dataWalletHash("data/wallet", "user", "password"));
dataWalletSend([data, wallet], admin, user, 100000.0, password5, md5(password6));

$gas_index = 6;
function gas(){
    return [
        gas_address => admin,
        gas_key => password . $GLOBALS[gas_index],
        gas_next_hash => md5(password . (++$GLOBALS[gas_index])),
    ];
}

function sendGasForScript($address, $script){
    assertEquals("testReg $script", http_post_json("localhost/wallet/api/reg.php", [
        address => $address,
        next_hash => md5(password1),
    ])[success], true);

    assertEquals("testSend $script", http_post_json("localhost/wallet/api/send.php", [
            from_address => admin,
            to_address => $address,
            password => password . $GLOBALS[gas_index],
            next_hash => md5(password . ++$GLOBALS[gas_index]),
            amount => 100000,
        ] + gas())[success], true);

    assertEquals("testDelegate $script", http_post_json("localhost/wallet/api/delegate.php", [
            address => $address,
            password => password1,
            script => $script,
        ] + gas())[success], true);
}

sendGasForScript(gas_giveaway, "data/giveaway");
sendGasForScript(usdt_reg, "usdt/reg/reg");
sendGasForScript(usdt_deposit, "usdt/deposit/start");
sendGasForScript(usdt_check, "usdt/deposit/check");

echo $gas_index;