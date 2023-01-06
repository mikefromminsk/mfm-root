<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$package = get_required(package);

$filename = "buffer/$package.zip";
$temp_dir = "buffer/_" . $package;
unlink($filename);

$response = [];

function scanDirs($absolute_path)
{
    $local_path = str_replace($GLOBALS[package_path], "", $absolute_path);
    if (is_dir($absolute_path)) {
        $files = glob($absolute_path . '*', GLOB_MARK);
        foreach ($files as $file)
            scanDirs($file);
        rmdir($absolute_path);
    } else {
        $file_path = $GLOBALS[package] . str_replace($GLOBALS[temp_dir], "", $local_path);
        $file = selectWhere("files", [path => $file_path]);
        if ($file == null)
            $file = [
                path => $file_path,
                prev_key => ""
            ];
        $GLOBALS[response][] = $file;
        unlink($absolute_path);
    }
}

if (!empty($_FILES) && !empty($_FILES[archive][name])) {
    mkdir(buffer);
    if (move_uploaded_file($_FILES[archive][tmp_name], $filename)) {
        $zip = new ZipArchive();
        $res = $zip->open($filename);
        if ($res === true) {
            mkdir($temp_dir);
            $zip->extractTo($temp_dir);
            $zip->close();
            scanDirs($temp_dir);
        } else {
            error("qwfff");
        }
        unlink($filename);
    }
}

$response_str = json_encode($response);

header("Content-type: application/json");
header("Content-Disposition: attachment; filename=response.json");
header("Content-length: " . strlen($response_str));
header("Pragma: no-cache");
header("Expires: 0");

echo $response_str;