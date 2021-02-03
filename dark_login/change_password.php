<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

$email = get_required("email");
$old_password = get_required("old_password");
$new_password = get_required("new_password");

$old_password_hash = hash_sha56($old_password);
$new_password_hash = hash_sha56($new_password);

domain_put($email, $old_password_hash, $new_password_hash, null);

$domain = domain_get($email);
$new_password_hash = hash_sha56($new_password_hash);
$response["success"] = ($new_password_hash == $domain["domain_key_hash"]);

echo json_encode_readable($response);

