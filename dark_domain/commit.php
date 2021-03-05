<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$domain_name = get("domain_name");
$domain_key = get("domain_key");
$domain_next_key = get("domain_next_key", $domain_key);

description("commit");

$ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
$ignore_list[] = "app.zip";

foreach (scandir($_SERVER["DOCUMENT_ROOT"]) as $app_name) {
    if ($app_name != null && $app_name != $domain_name) continue;

    $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
    if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {

        //create app.zip
        $local_ignore_list = array_merge($ignore_list, explode("\r\n", file_get_contents($path . "/.gitignore")));
        $zip = new ZipArchive();
        $zipPath = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name . "/app.zip";
        $zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        foreach (file_list_rec($path, $local_ignore_list) as $file_absolute_path) {
            $file_local_path = substr($file_absolute_path, strpos($file_absolute_path, "/", strlen($_SERVER["DOCUMENT_ROOT"]) + 1) + 1);
            $zip->addFile($file_absolute_path, $file_local_path);
        }
        $zip->close();

        domain_set($host_name, $domain_name, $domain_key, hash_sha56($domain_next_key), hash_file(HASH_ALGO, $zipPath));

        //domain_repo_set($app_name, $zipPath);
    }
}
