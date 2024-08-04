<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

const PROVIDERS = [
    "BSC" => [
        name => "BSC",
        title => "BSC BEP-20",
        contract => '0x55d398326f99059ff775485246999027b3197955',
        min_deposit => 5,
        fee => 1,
        deposit_addresses => [
            "0x1e0426Ba2E77eDdf7FfB19C57B992c4dcC6455F4",
        ],
        deadline_interval => 60 * 30,
    ],
    "TRON" => [
        name => "TRON",
        title => "Tron TRC-20",
        min => 0.02,
        contract => 'TR7NHqjeKQxGTCi8q8ZY4pL8otSzgjLj6t',
        min_deposit => 5,
        fee => 1,
        deposit_addresses => [
            "TPWZ6TNgYBCh18Bf4EVfKesoHHRJ4w8SgT",
            "TSXvWWCsysLQoPujPCEbYQySXP66ZvN57b",
        ],
        deadline_interval => 60 * 30,
    ],
];

