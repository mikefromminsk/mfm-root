<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/api/utils.php";

$token = get_required(token);

if ($user_id == null){
    $user_id = selectRowWhere(users, [token => $token])[user_id];
}

if ($user_id == null) {
    $user_id = createUser($token, get_string(email));
}
