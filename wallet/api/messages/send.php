<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/properties.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(gas_address);
$to_address = get_required(to_address);
$random_id = get_required(random_id);
$prev_id = get_string(prev_id);
$message = get_required(message);
$fcm_server_key = get_required(fcm_server_key);

$token = get_string(token);

if ($token != null && dataGet([wallet, tokens, $from_address]) != $token)
    dataSet([wallet, tokens, $from_address], $token);

$response[success] = dataSet([wallet, dialogs, $from_address, $to_address], [
    id => $random_id,
    text => $message,
    prev_id => $prev_id,
]);

commit($response);

http_post("https://fcm.googleapis.com/fcm/send", [
    to => $token,
    notification => [
        title => $from_address,
        body => $message,
    ],
], [
    Authorization => "Bearer $fcm_server_key"
]);