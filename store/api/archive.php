<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$rootPath = $_SERVER["DOCUMENT_ROOT"] . "/" . $domain;

$zip = new ZipArchive();
$zip->open($domain . '.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

$filesToDelete = array();

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $zip->addFile($filePath, $relativePath);
        $filesToDelete[] = $filePath;
    }
}
$zip->close();

$response[success] = true;

echo json_encode($response);

