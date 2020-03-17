<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_key = get_required("domain_key");

$ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
$ignore_list[] = "properties_overload.php";
$ignore_list[] = "node/files";

foreach (scandir($_SERVER["DOCUMENT_ROOT"]) as $app_name) {
    $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
    if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {

        $domain = domain_get($app_name);
        if ($domain == null) {
            domain_set($app_name, null, domain_key_hash("init", null), null);
            foreach (file_list_rec($path, $ignore_list) as $file_absolute_path) {
                $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
                $hash = hash_file(HASH_ALGO, $file_absolute_path);
                copy($file_absolute_path, $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash);
                insertList("files", array(
                    "domain_name" => $app_name,
                    "file_path" => $file_local_path,
                    "file_level" => substr_count($file_local_path, "/"),
                    "file_size" => filesize($file_absolute_path),
                    "file_hash" => $hash,
                ));
            }
            $server_repo_hash = hash(HASH_ALGO, domain_repo_get($app_name));
            domain_set($app_name, "init", domain_key_hash($domain_key, $server_repo_hash), $server_repo_hash);
            update("update servers set server_repo_hash = '" . uencode($server_repo_hash) . "'"
                . " where domain_name = '" . uencode($app_name) . "' and server_host_name = '" . uencode($host_name) . "'");
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
