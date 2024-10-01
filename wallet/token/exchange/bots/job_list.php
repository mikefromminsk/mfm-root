<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/mfm-db/db.php";

$tokens = [
    [
        domain => "exchange",
        address => "admin",
        password => "pass",
    ]
];


foreach ($tokens as $token) {
    requestEquals("/" . $token[domain] . "/api/exchange/bot_pump.php", [
        domain => $token[domain],
        address => $token[address],
        password => $token[password],
    ], success, true, false);
    requestEquals("/" . $token[domain] . "/api/exchange/bot_spred.php", [
        domain => $token[domain],
    ], success, true, false);
}