<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$domain = get_required(domain);
$info = get_required(info);

$info = json_decode($info, true);

if ($gas_address != admin) error("only admin can insert recipe");

dataSet([world, info, $domain], $info);

if ($info[recipe] != null)
    dataSet([world, recipe, $domain], $info[recipe]);

commit();