<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/utils.php";

$login = get_required("login");
$password = get_required("password");

description("login on server");

$user_password_hash = hash_sha56($password);


dataId("users", $login, $admin_token);
dataSet("users.$login", "password_hash", $admin_token, $user_password_hash);

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_stock/login.php";