<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$path = get_path_required(path);
$question = get_required(question);

$answers = [];
$values = [];
for ($i = 0; $i < 20; $i++) {
    $answers[] = get_string(answer . $i);
    $values[] = get_string(value . $i);
}


$next_hash = get_required(next_hash);
$amount = get_required(amount);


dataSet([$path, question], $question);
dataSet([$path, answers], null);
dataSet([$path, value], null);
for ($i = 0; $i < sizeof($answers); $i++) {
    dataSet([$path, answers, answer . $i], $answers[$i]);
    dataSet([$path, answers, value . $i], $values[$i]);
}
dataWalletInit([$path, wallet], $GLOBALS[gas_address], $next_hash, $amount);

$response[success] = true;

commit($response);