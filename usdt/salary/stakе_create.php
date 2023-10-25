<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);
$from_password = get_required(from_password);
$from_next_hash = get_required(from_next_hash);
$to_password = get_required(to_password);
$amount = get_int_required(amount);
$wallet_path = get_required(wallet_path, "data/wallet");
$script_path = get_required(script_path, "stake/stake_mint");
$developer_name = get_required(developer_name);
$developer_address = get_required(developer_address);
$portion = get_int_required(portion);

$wallet_path = explode("/", $wallet_path);

$response[success] = dataWalletSend($wallet_path, $from_address, $to_address, $amount, $from_address, $from_next_hash);
dataWalletDelegate($wallet_path, $to_address, $to_password, $script_path);
dataSet([data, developers, $developer_name], [
    wallet_path => $wallet_path,
    from_address => $to_address,
    portion => $portion,
    developer_address => $developer_address,
]);

commit($response);
