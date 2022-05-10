<?php

include_once "auth.php";

$rewards = array_to_map(selectWhere(transfers, [type => DROP]), parameter);

$drops = selectWhere(drops, []);

foreach ($drops as &$drop) {
    $drop[rewarded] = $rewards[$drop[drop_id]] != null;
}

$response[drops] = $drops;

echo json_encode($response);