<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$rootPath = $_SERVER["DOCUMENT_ROOT"] . "/" . $domain;

$ignoreList = file_get_contents("$_SERVER[DOCUMENT_ROOT]/$domain/.gitignore");
$ignoreList = explode("\r\n", $ignoreList);
if ($domain != "drc1")
    $ignoreList[] = "api/token";
$ignoreList[] = "logo.svg";
$ignoreList[] = ".gitignore";
$ignoreList = str_replace("\\", DIRECTORY_SEPARATOR, $ignoreList);
$ignoreList = str_replace("/", DIRECTORY_SEPARATOR, $ignoreList);


$zip = new ZipArchive();
$zip->open("$_SERVER[DOCUMENT_ROOT]/wallet/apps/$domain.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $ignore = false;
        foreach ($ignoreList as $item) {
            if (strpos($relativePath, $item) !== false) {
                $ignore = true;
                break;
            }
        }
        if ($ignore) continue;
        $relativePath = implode("/", explode(DIRECTORY_SEPARATOR, $relativePath));
        $zip->addFile($filePath, $relativePath);
    }
}
$zip->close();

$response[success] = true;

echo json_encode($response);

