<?php

include_once "auth.php";

$stake_id = get_int_required(stake_id);

$response[result] = stake_close($user_id, $stake_id);

echo json_encode($response);