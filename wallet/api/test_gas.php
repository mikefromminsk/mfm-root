<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/test.php"; // todo problem start without it
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$gas_index = 1;
function gas()
{
    return [
        gas_address => admin,
        gas_key => pass . $GLOBALS[gas_index],
        gas_next_hash => md5(pass . (++$GLOBALS[gas_index])),
    ];
}

assertEquals("launch $gas_domain", http_post($GLOBALS[host_name] . "/wallet/api/launch.php", [
        domain => $gas_domain,
        address => admin,
        next_hash => md5(pass1),
        amount => 1000000000,
    ] + gas())[success]);


function send($domain, $address, $key = null, $hash = null, $amount = 10000, $script = null)
{
    assertEquals("reg $address", http_post($GLOBALS[host_name] . "/$domain/api/token/reg.php", [
            address => $address,
            next_hash => md5(pass),
        ] + gas())[success]);

    if ($key == null) {
        $key = pass . $GLOBALS[gas_index];
        $hash = md5(pass . ++$GLOBALS[gas_index]);
    }

    assertEquals("send $address", http_post($GLOBALS[host_name] . "/$domain/api/token/send.php", [
            from_address => admin,
            to_address => $address,
            password => $key,
            next_hash => $hash,
            amount => $amount,
        ] + gas())[success]);

    if ($script != null)
        assertEquals("delegate $script", http_post($GLOBALS[host_name] . "/$domain/api/token/delegate.php", [
                address => $address,
                password => pass,
                script => $script,
            ] + gas())[success]);
}

send($gas_domain, "free_reg", null, null, 10000, "$gas_domain/api/token/free_reg.php");
send($gas_domain, "change_pass", null, null, 10000, "$gas_domain/api/token/change_pass.php");

assertEquals("change pass", http_post($GLOBALS[host_name] . "/$gas_domain/api/token/change_pass.php", [
        address => admin,
        key => pass . $GLOBALS[gas_index],
        next_hash => dataWalletHash($gas_domain, admin, pass),
    ])[success]);