<?php

include_once "utils.php";

$referrer_code = get_int(referrer_code);

if ($referrer_code != null) {
    $user = selectRowWhere(users, [referrer_code => $referrer_code]);
    $drop = selectRowWhere(drops, [drop_id => $user[drop_id]]);
    if ($drop != null) {
        $transfer = selectRowWhere(transfers, [type => DROP, parameter => $drop[drop_id]]);
        if ($transfer == null) {
            $coin = selectRowWhere(coin, [ticker => $drop[ticker]]);
            transfer(DROP, $coin[drop_user_id], $user[user_id], $drop[ticker], $drop[reward], $drop[drop_id]);
        }
    }
}

if ($user != null)
    $response[token] = $user[token];

$response[result] = $response[token] != null;

echo json_encode($response);