<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_string(search_text);

$response[result] = dataSearch("wallet/info", $search_text);

commit($response);