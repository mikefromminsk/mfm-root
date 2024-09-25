<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$address = get_required(address);
$domain = get_required(domain);

$response[balance] = worldBalance($domain, [world, avatar, $address]);

commit();