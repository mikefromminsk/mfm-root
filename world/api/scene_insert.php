<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);
$width = get_int_required(width);
$height = get_int_required(height);

if (!dataExist([$scene])) {
    dataSet([$scene, settings, width], $width);
    dataSet([$scene, settings, height], $height);
}

commit();