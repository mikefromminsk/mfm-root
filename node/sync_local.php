<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_key = get_required("domain_key");

function file_list_rec($dir, &$ignore_list, &$results = array())
{
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . "/" . $value);
        $path = str_replace("\\", "/", $path);
        $ignore = false;
        foreach ($ignore_list as $ignore_item)
            $ignore = $ignore || (strpos($path, $ignore_item) !== false);
        if ($ignore)
            continue;
        if (!is_dir($path)) {
            $results[] = $path;
        } else if ($value != "." && $value != "..") {
            file_list_rec($path, $ignore_list, $results);
        }
    }
    return $results;
}

$ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
$ignore_list[] = "properties_overload.php";
$ignore_list[] = "node/files";

foreach (scandir($_SERVER["DOCUMENT_ROOT"]) as $app_name) {
    $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
    if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {
        $domain = domain_get($app_name);
        if ($domain == null) {
            $server_group_id = domain_set($app_name, null, domain_key_hash("init", null), null);
            foreach (file_list_rec($path, $ignore_list) as $file_absolute_path) {
                $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
                $hash = hash_file(HASH_ALGO, $file_absolute_path);
                copy($file_absolute_path, $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash);
                insertList("files", array(
                    "server_group_id" => $server_group_id,
                    "file_path" => $file_local_path,
                    "file_level" => substr_count($file_local_path, "/"),
                    "file_size" => filesize($file_absolute_path),
                    "file_hash" => $hash,
                ));
            }
            $server_repo_hash = hash(HASH_ALGO, domain_repo_get($server_group_id));
            domain_set($app_name, "init", domain_key_hash($domain_key, $server_repo_hash), $server_repo_hash);
            update("update servers set server_repo_hash = '" . uencode($server_repo_hash) . "'"
                . " where server_group_id = $server_group_id and server_host_name = '" . uencode($host_name) . "'");
        } else {
            $repo = [];
            foreach (file_list_rec($path, $ignore_list) as $file_absolute_path) {
                $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
                $repo[$file_local_path] = file_get_contents($file_absolute_path);
            }
            domain_repo_set($app_name, json_encode($repo));
        }
    }
}
