<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

$file = get_required("file");
$domain_name = get_required("domain_name");
$password = get_required("password");


$domains = domain_put($domain_name, null, $password, $temp_zip);

if (sizeof($domains) == 0) error("password is not correct");

$app_dir = $_SERVER["DOCUMENT_ROOT"] . "/" . $domain_name;

if (!file_exists($app_dir)) if (!mkdir($app_dir)) error("create dir error");

$app_zip = $app_dir . "/app.zip";
$temp_zip = $app_dir . "/temp.zip";
if (file_exists($temp_zip)) if (!unlink($temp_zip)) error("delete temp file error");

if ($file["type"] == "application\/x-zip-compressed") {
    if (!move_uploaded_file($file['tmp_name'], $temp_zip)) error("file not uploaded");
} else {
    $zip = new ZipArchive();
    $zip->open($temp_zip, ZipArchive::CREATE | ZipArchive::OVERWRITE);
    $zip->addFile($file["tmp_name"], $file["name"]);
    $zip->close();
}

if (file_exists($app_zip)) if (!unlink($app_zip)) error("delete zip file error");
if (!rename($temp_zip, $app_zip)) error("rename error");

$zip = new ZipArchive();
if ($zip->open($app_zip)) {
    $zip->extractTo($app_dir);
    $zip->close();
} else {
    error("extract zip error");
}

$response["domains"] = $domains;

echo json_encode_readable($response);


