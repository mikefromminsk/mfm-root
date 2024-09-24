<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);
$pos = get_required(pos);

$response[chest] = dataObject([world, $scene, blocks, $pos, inventory], 100);

commit($response);