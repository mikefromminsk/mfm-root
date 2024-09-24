<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

dataSet([wallet, recipes, $domain], true);
commit();