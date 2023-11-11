<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

echo json_encode("$host_name") . "\n";

$data_path = "data/wallet";

dataWalletInit($data_path, admin, md5(password), 100000000.0);
assertEquals("dataWalletInit", dataGet([data, wallet, admin, amount]), 100000000.0);

dataWalletReg($data_path, user1, md5(password));
dataWalletSend($data_path, admin, user1, 2000.0, password, md5(password2));
dataWalletSend($data_path, user1, admin, 1.0, password, md5(password2));
assertEquals("dataSend", dataGet([data, wallet, admin, amount]), 100000000.0 - 2000.0 + 1.0);

dataWalletDelegate($data_path, user1, password2, "wallet/api/testDelegate");

$_POST[gas_address] = admin;
$_POST[gas_key] = password2;
$_POST[gas_next_hash] = md5(password3);
commit("commit");
echo "\n";

assertEquals("testDelegate", http_post_json("$host_name/wallet/api/testDelegate.php", [])[success], true);
assertEquals("balanceAfterBurn", strval(dataGet([data, wallet, user1, amount])), strval(1999 - FILE_ROW_SIZE));

dataWalletReg($data_path, reg, md5(password));
dataWalletDelegate($data_path, reg, password, "data/api/token/free_reg");
dataWalletSend($data_path, admin, reg, 100000.0, password3, md5(password4));

dataWalletReg($data_path, test1, dataWalletHash("data/wallet", "test1", "password"));
dataWalletSend($data_path, admin, test1, 200.0, password4, md5(password5));

dataWalletReg($data_path, user, dataWalletHash("data/wallet", "user", "pass"));
dataWalletSend($data_path, admin, user, 100000.0, password5, md5(password6));

$gas_index = 6;
function gas()
{
    return [
        gas_address => admin,
        gas_key => password . $GLOBALS[gas_index],
        gas_next_hash => md5(password . (++$GLOBALS[gas_index])),
    ];
}

function sendGasForScript($address, $script)
{
    assertEquals("testReg $script", http_post_json($GLOBALS[host_name] . "/data/api/token/free_reg.php", [
        address => $address,
        next_hash => md5(password1),
    ])[success], true);

    assertEquals("testSend $script", http_post_json($GLOBALS[host_name] . "/data/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => password . $GLOBALS[gas_index],
            next_hash => md5(password . ++$GLOBALS[gas_index]),
            amount => 100000,
        ] + gas())[success], true);

    assertEquals("testDelegate $script", http_post_json($GLOBALS[host_name] . "/data/api/token/delegate.php", [
            address => $address,
            password => password1,
            script => $script,
        ] + gas())[success], true);
}

sendGasForScript(gas_giveaway, "data/api/token/drop");
sendGasForScript(usdt_reg, "usdt/reg/reg");
sendGasForScript(usdt_deposit, "usdt/deposit/start");
sendGasForScript(usdt_check, "usdt/deposit/check");


assertEquals("archive data", http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => "data",
])[success], true);
upload("data", $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/data.zip");
assertNotEquals("upload data", sizeof(dataKeys([store, "data"])), 0);


assertEquals("archive usdt", http_post_json($GLOBALS[host_name] . "/wallet/contracts/archive.php", [
    domain => "gas",
])[success], true);

upload("usdt", $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
assertNotEquals("upload usdt", sizeof(dataKeys([store, "usdt"])), 0);
dataWalletInit("usdt/wallet", "admin", md5(pass), 10000000);


upload("gas", $_SERVER["DOCUMENT_ROOT"] . "/wallet/contracts/gas.zip");
assertNotEquals("upload gas", sizeof(dataKeys([store, "gas"])), 0);
dataWalletInit("gas/wallet", "admin", md5(pass), 10000000);


assertEquals("ico sell", http_post_json($GLOBALS[host_name] . "/data/api/token/ico_sell.php", [
    address => "admin",
    key => "pass",
    next_hash => md5(pass2),
    amount => 100000,
    price => 34,
])[success], true);

assertEquals("ico buy", http_post_json($GLOBALS[host_name] . "/data/api/token/ico_buy.php", [
    address => "admin",
    key => "pass",
    next_hash => md5(pass2),
    amount => 1000,
])[success], true);


echo $gas_index;