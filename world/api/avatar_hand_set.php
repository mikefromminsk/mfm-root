<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$domain = get_required(domain);

dataSet([world, avatar, hand], $domain);

commit();
