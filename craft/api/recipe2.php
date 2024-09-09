<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$domain = get_required(domain);

$response[recipe2][domain1]= dataGet([$domain, recipe2, domain1]);
$response[recipe2][domain2]= dataGet([$domain, recipe2, domain2]);

commit($response);