<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$path = get("path");

$file = file_get($domain_name, $path);

if ($file["file_data"] != null) {
    header("Content-type: " . mime_content_type(meta_data($file["file_name"])));
    echo meta_data($file["file_data"]);
} else {
    header("Content-type: text/directory;application/json;charset=utf-8");
    $result = array();
    $children = select("select * from files where file_parent_id = " . $file["file_id"]);
    foreach ($children as $child)
        $result[] = array(
            "name" => meta_data($child["file_name"]),
            "size" => meta_size($child["file_data"]),
            "data" => $child["file_data"],
        );
    echo json_encode($result);
}

