<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$gas_address = get_required(gas_address);
$domain = get_required(domain);
$recipe = get_required(recipe);

$recipe = json_decode($recipe, true);

if ($gas_address != admin) error("only admin can insert recipe");

dataSet([world, recipe, $domain], $recipe);

commit();