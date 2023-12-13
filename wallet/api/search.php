<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_required(search_text);

$GLOBALS[domains] = implode(",",    dataSearch("wallet/info", $search_text) ?: []);

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/list.php";
