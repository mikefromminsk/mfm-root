<?php

//include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/test.php";


$path = "/wefwe/wfewef/ww/";

$path = explode("/", $path);

echo json_encode($path);
echo json_encode(implode("/", $path));