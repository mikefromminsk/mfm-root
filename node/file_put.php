<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$path = get("path");
$domain_key = get("domain_key");
$domain_key_hash = get("domain_key_hash");
$data = get("data");

if ($data == null && (sizeof($_FILES) == 0))
    error("Data is empty" . $data);

domain_set($domain_name, $domain_key, $domain_key_hash);

$filemeta = getFile($domain_name, $path, true);

if (strlen($filemeta["file_data"]) == MAX_SMALL_DATA_LENGTH
    && scalar("select count(*) from files where file_data = '" . $filemeta["file_data"] . "'") == 1)
    if (!unlink($_SERVER["DOCUMENT_ROOT"] . "/node/files/" . substr($filemeta["file_data"], FILE_SIZE_HEX_LENGTH)))
        error("file cannot be replaced");

if ($data != null) {
    if (strlen($data) >= MAX_SMALL_DATA_LENGTH) {
        $hash = hash(HASH_ALGO, $data);
        $filemeta_size_hex = sprintf("%0" . FILE_SIZE_HEX_LENGTH . "X", strlen($data));
        if (!file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash, $data))
            error("file cannot be save to storage");
        updateList("files", array("file_data" => $filemeta_size_hex . $hash), "file_id", $filemeta["file_id"]);
    } else {
        updateList("files", array("file_data" => $data), "file_id", $filemeta["file_id"]);
    }
}
if (sizeof($_FILES) != 0) {
    foreach ($_FILES as $key => $file) {
        if ($file["error"] > 0) error("file upload error");
        if ($file["size"] >= MAX_SMALL_DATA_LENGTH) {
            $hash = hash_file(HASH_ALGO, $file["tmp_name"]);
            $file_size_hex = sprintf("%0" . FILE_SIZE_HEX_LENGTH . "X", $file["size"]);
            if (!move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash))
                error("file cannot be save to storage");
            updateList("files", array(
                "file_data" => $file_size_hex . $hash
            ), "file_id", $filemeta["file_id"]);
        } else {
            $data = file_get_contents($file["tmp_name"]);
            updateList("files", array(
                "file_data" => $data
            ), "file_id", $filemeta["file_id"]);
            unlink($file["tmp_name"]);
        }
    }
}



