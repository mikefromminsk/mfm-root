<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$path = get("path");

$file = getFile($domain_name, $path);

if ($file["file_data"] == null) {
    header("Content-type: text/directory;application/json;charset=utf-8");
    $result = array();
    $children = select("select * from files where file_parent_id = " . $file["file_id"]);
    foreach ($children as $child)
        $result[getData($child["file_name"])] = getSize($child["file_data"]);
    echo json_encode($result);
} else {
    header("Content-type: " . mime_content_type(getData($file["file_name"])));
    echo getData($file["file_data"]);
}

