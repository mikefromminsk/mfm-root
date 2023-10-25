<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$file = get_required(file);
$domain = get_required(domain);

$file_hash = hash_file(md5, $file[tmp_name]);
//if (voteApprove([$domain]) != $file_hash) error("votes not equal to file");
// error(json_encode($file));
$zip = new ZipArchive;
if ($zip->open($file[tmp_name]) === TRUE) {
    $zip->extractTo($_SERVER["DOCUMENT_ROOT"] . "/" . $domain);
    $zip->close();
    $response[success] = true;
    commit($response);
} else {
    error("zip->open is false");
}

