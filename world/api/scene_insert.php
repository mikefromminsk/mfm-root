<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$scene = get_required(scene);
$width = get_int_required(width);
$height = get_int_required(height);
$texture = get_required(texture);

if (dataExist([world, $scene])) error("scene already exists");

dataSet([world, $scene, settings, width], $width);
dataSet([world, $scene, settings, height], $height);
dataSet([world, $scene, settings, texture], $texture);

$response[succes] = true;

commit($response);