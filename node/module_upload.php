<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get("domain_name");
$domain_key = get_required("domain_key");

$ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
$ignore_list[] = "app.zip";

/*query("delete from files");
query("delete from servers");
query("delete from domains");*/

foreach (scandir($_SERVER["DOCUMENT_ROOT"]) as $app_name) {
    if ($domain_name != null && $app_name != $domain_name) continue;

    $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
    if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {

        $local_ignore_list = array_merge($ignore_list, explode("\r\n", file_get_contents($path . "/.gitignore")));

        $domain = domain_get($app_name);

        $zip = new ZipArchive();
        $zipPath = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name . "/app.zip";
        $zip->open($zipPath, ZipArchive::CREATE);
        if ($domain == null) {
            foreach (file_list_rec($path, $local_ignore_list) as $file_absolute_path) {
                $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
                $zip->addFile($file_absolute_path, $file_local_path);
                $hash = hash_file(HASH_ALGO, $file_absolute_path);
                insertList("files", array(
                    "domain_name" => $app_name,
                    "file_path" => $file_local_path,
                    "file_level" => substr_count($file_local_path, "/"),
                    "file_size" => filesize($file_absolute_path),
                    "file_hash" => $hash,
                ));
            }
            $zip->close();
            $server_repo_hash = hash_file(HASH_ALGO, $zipPath);
            domain_set($app_name, null, domain_key_hash($domain_key, $server_repo_hash), $server_repo_hash);
            update("update servers set server_repo_hash = '" . uencode($server_repo_hash) . "'"
                . " where domain_name = '" . uencode($app_name) . "' and server_host_name = '" . uencode($GLOBALS["host_name"]) . "' ");
        } else {
            foreach (file_list_rec($path, $local_ignore_list) as $file_absolute_path) {
                $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
                $zip->addFile($file_absolute_path, $file_local_path);
            }
            $zip->close();
            domain_repo_set($app_name, $zipPath);
        }
    }
}