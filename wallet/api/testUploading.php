<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$file = get_required(file);

if (move_uploaded_file($file[tmp_name], $file[name])){
    $response[success] = true;
    commit($response);
} else {
    error($file[error]);
}
