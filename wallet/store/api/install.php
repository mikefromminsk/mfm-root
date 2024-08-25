<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$app_domain = get_required(app_domain);

if ($app_domain != null && $filepath == null) {
    $filepath = $_SERVER[DOCUMENT_ROOT] . "/wallet/store/apps/$app_domain.zip";
}
$archive_hash = hash_file(md5, $filepath);
if (!$archive_hash) error("file hash is false in $filepath");
//if (dataGet([$domain, packages, $app_domain, hash]) == $archive_hash) error("archive was uploaded before");

$zip = new ZipArchive;
if ($zip->open($filepath) !== true) error("zip->open is false");

$zip->extractTo($_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $domain);

$files = [];
for ($i = 0; $i < $zip->numFiles; $i++) {
    $filename = $zip->getNameIndex($i);
    $filepath = $domain . "/" . $filename;
    $filepath = implode("/", explode("\\", $filepath));
    $file_hash = hash_file(md5, $_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $filepath);
    $files[$file_hash] = $filepath;
    $GLOBALS[gas_bytes] += 1;
}
dataSet([wallet, $domain, packages, $app_domain, hash], $archive_hash);
dataSet([wallet, $domain, contracts], $files);
$zip->close();

$response[file_hash] = $file_hash;

$response[success] = true;

commit($response);
