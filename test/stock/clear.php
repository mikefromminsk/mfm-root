<?php
include_once "../db.php";
$access = get_required("access");
$message = null;

$message = $access == "admin" ? null : "access error";

if ($message == null) {

    query("delete from users");
    query("delete from domains");
    query("delete from domain_keys");
    query("delete from offers");
    query("delete from coins");

}

echo json_encode(array(
    "message" => $message,
));
