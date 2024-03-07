<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text, "");

$GLOBALS[domains] = implode(",", dataSearch("wallet/info", $search_text) ?: []);

if ($GLOBALS[domains]) {
    include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/list.php";
} else {
    $response[result] = [];
    commit([]);
}
