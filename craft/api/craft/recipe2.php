<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$domain1 = get_required(domain1);
$domain2 = get_required(domain2);

recipe2($domain1, $domain2);

commit();