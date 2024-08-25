<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

function uploadContent($domain, $filepath, $local_path)
{
    $zip = new ZipArchive;
    if ($zip->open($filepath) !== true) error("zip->open is false");

    for ($i = 0; $i < $zip->numFiles; $i++) {
        if (!in_array(pathinfo($zip->getNameIndex($i))[extension], [jpg, svg])) {
            error("file extension is not correct");
        }
    }
    $domain_folder = $_SERVER[DOCUMENT_ROOT] . DIRECTORY_SEPARATOR . $domain . DIRECTORY_SEPARATOR;
    $local_folder = $domain_folder . $local_path . DIRECTORY_SEPARATOR;
    $temp_folder = $local_folder . temp . DIRECTORY_SEPARATOR;
    $zip->extractTo($temp_folder);

    $files = [];
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $extension = strtolower(pathinfo($zip->getNameIndex($i))[extension]);
        $hash = hash_file(md5, $temp_folder . $zip->getNameIndex($i));
        $target_filename = "$local_folder$hash.$extension";
        mkdir($local_folder, 0777, true);
        unlink($target_filename);
        rename($temp_folder . $zip->getNameIndex($i), $target_filename);
        $files[$hash] = $hash . "." . $extension;
    }

    $zip->close();
    return $files;
}




