<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$from_address = get_required(from_address);
$to_address = get_required(to_address);

$from_id_list = dataHistory([wallet, dialogs, $from_address, $to_address, id]);
$from_text_list = dataHistory([wallet, dialogs, $from_address, $to_address, text]);
$from_prev_id_list = dataHistory([wallet, dialogs, $from_address, $to_address, prev_id]);

$to_id_list = dataHistory([wallet, dialogs, $from_address, $to_address, id]);
$to_text_list = dataHistory([wallet, dialogs, $from_address, $to_address, text]);
$to_prev_id_list = dataHistory([wallet, dialogs, $from_address, $to_address, prev_id]);

$messages = [];

foreach ($from_text_list as $index => $text) {
    $id = $from_id_list[$index];
    $prev_id = $from_prev_id_list[$index];
    $messages[$id] = [
        id => $id,
        text => $text,
        prev_id => $prev_id,
        from_me => true,
    ];
}

foreach ($to_text_list as $index => $text) {
    $id = $to_prev_id_list[$index];
    $prev_id = $to_prev_id_list[$index];
    $messages[$id] = [
        id => $id,
        text => $text,
        prev_id => $prev_id,
        from_me => false,
    ];
}

$dialog = [];

foreach ($messages as $id => $message) {
    if ($message[next_id] != null) {
        array_splice($dialog, 3, 0, ['x']);
    } else {

    }
}

$response[messages] = array_values($messages);

commit($response);