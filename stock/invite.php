<?php

include_once "api/utils.php";

$ticker = get_required("t");
$inviter = get_required("i");

$token = random_key(users, token);

$user_id = createUser($token, get_string(email));

$drop = selectRowWhere(drops, [ticker => $ticker]);

$referrer_code = random_key(users, referrer_code);

updateWhere(users, [inviter => $inviter, drop_id => $drop[drop_id], referrer_code => $referrer_code], [user_id => $user_id]);

redirect("https://play.google.com/store/apps/details?id=com.zhiliaoapp.musically&hl=ru&gl=US&referrer=$referrer_code");
