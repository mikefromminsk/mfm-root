<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$categoriesMap = [];
foreach (dataKeys([store, info]) as $domain) {
    $category = dataGet([store, info, $domain, category]);
    if ($category != "")
        $categoriesMap[$category] = true;
}

$response[result] = array_keys($categoriesMap);

commit($response);