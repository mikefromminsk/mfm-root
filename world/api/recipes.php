<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$response[recipes] = dataObject([world, recipe], 100);

commit($response);