<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$package = get_required(package);
$package_path = $_SERVER["DOCUMENT_ROOT"] . "/" . $package . "/";
$ignore_list = explode("\r\n", file_get_contents($package_path . "/.gitignore"));

$archive_file_name = "buffer/$package.zip";
mkdir("buffer");

$zip = new ZipArchive();
$zip->open($archive_file_name, ZipArchive::CREATE | ZipArchive::OVERWRITE);

function scanDirs($absolute_path)
{
    $local_path = str_replace($GLOBALS[package_path], "", $absolute_path);
    if (!in_array($local_path, $GLOBALS[ignore_list]))
        if (is_dir($absolute_path)) {
            $files = glob($absolute_path . '*', GLOB_MARK);
            foreach ($files as $file)
                scanDirs($file);
        } else {
            $GLOBALS[zip]->addFile($absolute_path, $local_path);
        }
}

scanDirs($package_path);

$zip->close();

header("Content-type: application/zip");
header("Content-Disposition: attachment; filename=$package.zip");
header("Content-length: " . filesize($archive_file_name));
header("Pragma: no-cache");
header("Expires: 0");
readfile($archive_file_name);

unlink($archive_file_name);
