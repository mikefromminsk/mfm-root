<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/init.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

$email = get_required("email");
$password = get_required("password");

$password_hash = hash_sha56($password);
domain_put($email, null, $password_hash, null);
$domain = domain_get($email);
$password_hash = hash_sha56($password_hash);
$response["success"] = ($password_hash == $domain["domain_key_hash"]);
echo json_encode_readable($response);

