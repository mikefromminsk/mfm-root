<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);
$address = get_required(address);

$response[scene] = dataObject([$scene], 1000);
$response[avatar] = dataObject([avatar, $address], 1000) ?: [];

commit($response);
