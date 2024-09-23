<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$rootPath = $_SERVER["DOCUMENT_ROOT"] . "/$domain/api/$domain";

$zip = new ZipArchive();
$zip->open("$_SERVER[DOCUMENT_ROOT]/wallet/store/apps/$domain.zip", ZipArchive::CREATE | ZipArchive::OVERWRITE);
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);
        $relativePath = implode("/", explode(DIRECTORY_SEPARATOR, $relativePath));
        $zip->addFile($filePath, "api/$domain/$relativePath");
    }
}
$zip->close();

$archive_hash = hash_file(md5, $_SERVER[DOCUMENT_ROOT] . "/wallet/store/apps/$domain.zip");
dataSet([wallet, $domain, packages, $domain, hash], $archive_hash);

commit();

