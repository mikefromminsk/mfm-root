<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/properties.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(gas_address);
$to_address = get_required(to_address);
$message = get_required(message);

$token = get_string(token);

if ($token != null && dataGet([wallet, tokens, $from_address]) != $token)
    dataSet([wallet, tokens, $from_address], $token);

if (strcmp($from_address, $to_address) > 0) {
    $first_address = $from_address;
    $second_address = $to_address;
} else {
    $first_address = $to_address;
    $second_address = $from_address;
}
$dialog_id = "$first_address$second_address";

if (!dataExist([wallet, dialogs, $from_address, $to_address])) {
    dataSet([wallet, dialogs, $from_address, $to_address], $dialog_id);
    dataSet([wallet, dialogs, $to_address, $from_address], $dialog_id);
}

$response[success] = dataSet([wallet, messages, $dialog_id], [
    text => $message,
    from => $from_address,
]);

$fcm_server_key = get_required(fcm_server_key);
$to_token = dataGet([wallet, tokens, $to_address]);
if ($to_token == null) error("token is null");

http_post("https://fcm.googleapis.com/fcm/send", [
    to => $to_token,
    notification => [
        title => $from_address,
        body => $message,
    ],
], [
    Authorization => "Bearer $fcm_server_key"
]);

commit($response);
