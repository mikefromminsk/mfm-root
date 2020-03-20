<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";


$repo_path = $_SERVER["DOCUMENT_ROOT"] . "/search/app.zip";
$zip = new ZipArchive();
if ($zip->open($repo_path) == TRUE) {
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $file_path = $zip->getNameIndex($i);
        $file_data = $zip->getFromName($file_path);
        file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/$domain_name/$file_path", $file_data);
    }
}