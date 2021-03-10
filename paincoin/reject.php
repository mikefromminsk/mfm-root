<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$request_id = get_required("request_id");

description("reject");

$request = dataGet(["requests", $request_id], $admin_token);

$response["success"] = mail($request["email"], "Вы не выиграли paincoun", "Не твой день");

echo json_encode($response);
