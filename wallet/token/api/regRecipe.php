<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$type = get_required(type);

if (!dataExist([$domain, $type])) error("recipe2 not found");

dataSet([wallet, recipes, $domain], $type);
commit();