<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/utils.php";

$login = get_required("login");
$password = get_required("password");
$promo_url = get("promo_url");

description("login on server");

$user_password_hash = hash_sha56($password);

dataNew([], $pass);
if (dataGet(["users", $login], $pass) != null) error("this account exists");

dataNew(["users", $login], $pass);
dataSet(["users", $login, "password_hash"], $pass, $user_password_hash);

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/login/login.php";