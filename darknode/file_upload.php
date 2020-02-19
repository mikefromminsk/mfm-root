<?php

//include_once $_SERVER["DOCUMENT_ROOT"] ."/db-utils/db.php";

$uploadfile = $_SERVER["DOCUMENT_ROOT"] . "/darknode/files/test.rzr";

//. basename($_FILES['userfile']['name'])

if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
    echo "success";
} else {
    echo "fail";
}

echo $uploadfile;