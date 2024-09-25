<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$from_path = get_required(from_path);
$to_path = get_required(to_path);
$domain = get_required(domain);
$amount = get_int_required(amount);

worldSend($domain, [$from_path], [$to_path], $amount);

commit();