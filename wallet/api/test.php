<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

echo json_encode("$host_name") . "\n";

$data_path = "data/wallet";

dataWalletInit($data_path, admin, md5(pass), 100000000.0);
assertEquals("dataWalletInit", dataGet([data, wallet, admin, amount]), 100000000.0);

dataWalletReg($data_path, user1, md5(pass));
dataWalletSend($data_path, admin, user1, 2000.0, pass, md5(pass2));
dataWalletSend($data_path, user1, admin, 1.0, pass, md5(pass2));
assertEquals("dataSend", dataGet([data, wallet, admin, amount]), 100000000.0 - 2000.0 + 1.0);

dataWalletDelegate($data_path, user1, pass2, "wallet/api/testDelegate");

$_POST[gas_address] = admin;
$_POST[gas_key] = pass2;
$_POST[gas_next_hash] = md5(pass3);
commit("commit");
echo "\n";

assertEquals("testDelegate", http_post_json("$host_name/wallet/api/testDelegate.php", [])[success], true);
assertEquals("balanceAfterBurn", strval(dataGet([data, wallet, user1, amount])), strval(1999 - FILE_ROW_SIZE));

dataWalletReg($data_path, reg, md5(pass));
dataWalletDelegate($data_path, reg, pass, "data/api/token/free_reg");
dataWalletSend($data_path, admin, reg, 100000.0, pass3, md5(pass4));

dataWalletReg($data_path, test1, dataWalletHash("data/wallet", "test1", "pass"));
dataWalletSend($data_path, admin, test1, 200.0, pass4, md5(pass5));

dataWalletReg($data_path, user, dataWalletHash("data/wallet", "user", "pass"));
dataWalletSend($data_path, admin, user, 100000.0, pass5, md5(pass6));

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
    assertEquals("testReg $script", http_post_json($GLOBALS[host_name] . "/data/api/token/free_reg.php", [
        address => $address,
        next_hash => md5(pass1),
    ])[success], true);

    assertEquals("testSend $script", http_post_json($GLOBALS[host_name] . "/data/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => pass . $GLOBALS[gas_index],
            next_hash => md5(pass . ++$GLOBALS[gas_index]),
            amount => 100000,
        ] + gas())[success], true);

    if ($script != null)
        assertEquals("testDelegate $script", http_post_json($GLOBALS[host_name] . "/data/api/token/delegate.php", [
                address => $address,
                password => pass1,
                script => $script,
            ] + gas())[success], true);
}

sendGas(gas_giveaway, "data/api/token/drop");
sendGas(usdt_reg, "usdt/reg/reg");
sendGas(usdt_deposit, "usdt/deposit/start");
sendGas(usdt_check, "usdt/deposit/check");


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


assertEquals("ico sell", http_post_json($GLOBALS[host_name] . "/gas/api/token/ico_sell.php", [
        address => "admin",
        key => "pass",
        next_hash => md5(pass2),
        amount => 100000,
        price => 34,
    ] + gas())[success], true);

assertEquals("ico sell balance owner",
    strval(dataGet([gas, wallet, ico, amount])), strval(100000));
assertEquals("ico price",
    strval(dataGet([gas, price])), strval(34));


sendGas(ico_test);
dataWalletReg("usdt/wallet", ico_test, md5(pass));

assertEquals("ico buy", http_post_json($GLOBALS[host_name] . "/gas/api/token/ico_buy.php", [
        address => ico_test,
        key => pass,
        next_hash => md5(pass2),
        amount => 1000,
    ] + gas()), true);

assertEquals("ico sell balance owner",
    strval(dataGet([gas, wallet, ico, amount])), strval(100000 - 1000));
assertEquals("ico buy balance usdt",
    strval(dataGet([usdt, wallet, ico_test, amount])), strval(1000000 - (1000 * 34)));

echo $gas_index;