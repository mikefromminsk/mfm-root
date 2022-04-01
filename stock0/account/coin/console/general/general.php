<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");
$title = get_required("title");
$description = get_required("description");

description(basename(__FILE__));

if (dataGet(["coins", $domain_name, "owner"], $pass) != $login) error("you are not owner of this coin");

$response["success"] = dataSet(["coins", $domain_name, "title"], $pass, $title);
$response["success"] = dataSet(["coins", $domain_name, "description"], $pass, $description);

echo json_encode($response);