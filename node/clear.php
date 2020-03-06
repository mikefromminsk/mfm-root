<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

query("delete from domains");
query("delete from files");
query("delete from servers");

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
        } else if($value != "." && $value != "..") {
            file_list_rec($path, $ignore_list, $results);
        }
    }
    return $results;
}

$ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
$rootfiles = scandir($_SERVER["DOCUMENT_ROOT"]);
foreach ($rootfiles as $app_name) {
    $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
    if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {
        $domain_key = random_id();
        $response = http_json_post($server_url . "node/domain_set.php", array(
            "domain_name" => $app_name,
            "domain_key_hash" => hash("sha256", $domain_key),
        ));
        $app_files = file_list_rec($path, $ignore_list);
        foreach ($app_files as $app_file) {
            $local_path = substr($app_file, strlen($_SERVER["DOCUMENT_ROOT"]) + 1);
            $path_items = explode("/", $local_path);
            $app_name = array_shift($path_items);
            $local_path = implode("/", $path_items);
            $response = http_json_post($server_url . "node/file_put.php", array(
                "domain_name" => $app_name,
                "path" => $local_path,
                "domain_key" => $domain_key,
                "data" => file_get_contents($app_file),
            ));
        }
    }
}
