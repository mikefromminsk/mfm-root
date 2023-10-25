<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$search_text = get_required(search_text);

if ($search_text == "") error("search text is empty");

$response[result] = selectList("select data_key from `data` where data_parent_id = 1 and data_key like '%$search_text%'") ?: [];

commit($response);
