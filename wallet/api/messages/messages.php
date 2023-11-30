<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);

if (strcmp($from_address, $to_address) > 0) {
    $first_address = $from_address;
    $second_address = $to_address;
} else {
    $first_address = $to_address;
    $second_address = $from_address;
}
$dialog_id = "$first_address$second_address";

$texts = dataHistory([wallet, messages, $dialog_id, text]);
$from = dataHistory([wallet, messages, $dialog_id, from]);

$messages = [];

foreach ($texts as $index => $text) {
    $messages[] = [
        text => $text,
        from_me => $from[$index] == $from_address,
    ];
}

$response[messages] = $messages;

commit($response);