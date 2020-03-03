<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/login.php";

$coin_code = get_required("coin_code");
$coin_count = get_int_required("coin_count");
$receiver_user_login = get_required("receiver_user_login");
$coin_code = strtoupper($coin_code);

$receiver = selectMap("select * from users where user_login = '$receiver_user_login'");
if ($receiver == null)
    error("receiver doesnt exist");
if ($receiver["user_id"] == $user["user_id"])
    error("you cannot send coins to yourself");

$where = " where user_id = $user_id and domain_name like '$coin_code%' limit $coin_count";
$domain_count = scalar("select count(*) from domains $where");
if ($domain_count < $coin_count)
    error("not enough coins");

update("update domains set user_id = " . $receiver["user_id"] . " $where");
send($receiver["user_id"], "New coins!", "You have received $coin_count $coin_code");
