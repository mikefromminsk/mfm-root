<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$files = get_required(files);

$files = json_decode(file_get_contents($files[tmp_name]), true);

description("set files");

$success = 0;
$failed = 0;
$failed_list = [];

foreach ($files as $new_file) {
    
    $path = $new_file["path"];
    $prev_key_hash = hash("sha256",$new_file["prev_key"]);
    $fileExist = updateWhere("files", array(
        "archived" => 1,
    ), array(
        "path" => $path,
        "archived" => 0,
        "key_hash" => $prev_key_hash,
    ));

    if ($fileExist == true) {
        insertRow("files", array(
            "path" => $path,
            "prev_key" => $new_file["prev_key"],
            "key_hash" => $new_file["key_hash"],
            "data_hash" => $new_file["data_hash"],
            "updated" => microtime(true),
        ));
    } else {
        $last_file = selectRowWhere("files", array(
            "path" => $path,
            "archived" => 0
        ));
        if ($last_file == null) {
            insertRow("files", array(
                "path" => $path,
                "updated" => microtime(true),
            ));
        } else {
            if ($last_file["key_hash"] == $prev_key_hash) {
                insertRow("files", $new_file);
            } else {
                $failed += 1;
                $failed_list[] = $new_file;
                continue;
            }
        }
    }
    $success += 1;
}


$response["success"] = $success;
$response["failed"] = $failed;
$response["failed_list"] = $failed_list;

echo json_encode($response);