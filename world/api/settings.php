<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$response[recipe] = dataObject([recipe], 1000);
$response[info] = dataObject([info], 1000);

commit($response);
