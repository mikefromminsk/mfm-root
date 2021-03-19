<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock/auth.php";

$search = get_required("search");

description(basename(__FILE__));

$logins = dataLike(["users"], $pass, "$search%", true, 0, 10);

$response["found"] = [];
foreach ($logins as $user_login) {
    if ($user_login != $login)
        $response["found"][] = array(
            "dialog_id" => pairName($login, $user_login),
            "login" => $user_login,
        );
}

echo json_encode($response);