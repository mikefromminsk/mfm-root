<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$response[world] = dataObject([""], 1000);

commit($response);
