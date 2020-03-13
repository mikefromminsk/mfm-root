<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$path = get_required("path");
$domain_key = get_required("domain_key");
$domain_key_hash = get("domain_key_hash");
$data = get("data");

$path = trim($path, "/");

if ($data == null && (sizeof($_FILES) == 0))
    error("data is empty");

$domain = domain_check($domain_name, $domain_key);
if ($domain === false)
    error("domain_set error");

$server_group_id = $domain["server_group_id"];

$prev_file_exist = scalar("select count(*) from files where server_group_id = $server_group_id and file_path = '" . uencode($path) . "'") == 0;

function updateData($server_group_id, $file_path, $file_size, $file_hash, $insertIfTrue)
{
    if ($insertIfTrue)
        return insertList("files", array(
            "server_group_id" => $server_group_id,
            "file_path" => $file_path,
            "file_level" => substr_count($file_path, "/"),
            "file_size" => $file_size,
            "file_hash" => $file_hash,
        ));
    else
        return update("update files set file_size = $file_size, file_hash = '$file_hash'"
            . " where server_group_id = $server_group_id and file_path = '" . uencode($file_path) . "'");
}

if ($data != null) {
    $hash = hash(HASH_ALGO, $data);
    if (!file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash, $data))
        error("file cannot be save to storage");
    updateData($server_group_id, $path, strlen($data), $hash, $prev_file_exist);
}

if (sizeof($_FILES) != 0) {
    foreach ($_FILES as $key => $file) {
        $file_path = $path . "/" . $file["tmp_name"];
        if ($file["error"] > 0) error("file upload error");
        $hash = hash_file(HASH_ALGO, $file["tmp_name"]);
        if (!move_uploaded_file($file["tmp_name"], $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash))
            error("file cannot be save to storage");
        updateData($server_group_id, $file_path, $file["size"], $hash, $prev_file_exist);
    }
}

$repo = domain_repo_get($server_group_id);

$server_repo_hash = $repo != null ? hash(HASH_ALGO, $repo) : null;

$server_group_id = domain_set($domain_name, $domain_key, $domain_key_hash, $server_repo_hash);

update("update servers set server_repo_hash = '" . uencode($server_repo_hash) . "'"
    . " where server_group_id = $server_group_id and server_host_name = '" . uencode($host_name) . "'");
