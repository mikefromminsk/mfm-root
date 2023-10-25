<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$path = get_required(path);
$question = get_required(question);

$answers = [];
$values = [];
for ($i = 0; $i < 20; $i++) {
    $answers[] = get_string(answer . $i);
    $values[] = get_string(value . $i);
}

$path = explode("/", $path);

dataSet([$path, question], $question);
dataSet([$path, answers], null);
dataSet([$path, value], null);
for ($i = 0; $i < sizeof($answers); $i++) {
    dataSet([$path, answers, answer . $i], $answers[$i]);
    dataSet([$path, answers, value . $i], $values[$i]);
}

$response[success] = true;

commit($response);