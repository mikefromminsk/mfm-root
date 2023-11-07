<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

// нужен второй сервер для синхронизации

$domain = get_required(domain);
$file = get_required(file);

$file_hash = hash_file(md5, $file[tmp_name]);

//if (dataGet([$domain, vote, value]) != $file_hash) error("votes not equal to file");
if (dataGet([$domain, hash]) == $file_hash) error("archive was uploaded before");

$zip = new ZipArchive;
if ($zip->open($file[tmp_name]) !== TRUE) error("zip->open is false");

$zip->extractTo($_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $domain);
dataSet([$domain, vote, last_uploaded], $file_hash);

$files = [];
for ($i = 0; $i < $zip->numFiles; $i++) {
    $filepath = $domain . "/" . $zip->getNameIndex($i);
    $filepath = implode("/", explode("\\", $filepath));
    $file_hash = hash_file(md5, $_SERVER["DOCUMENT_ROOT"] . DIRECTORY_SEPARATOR . $filepath);
    $files[$file_hash] = $filepath;
}

$response[files] = $files;

dataSet([store, $domain], $files);

$zip->close();

// spend gas tokens

$response[success] = true;
commit($response);
