<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_domain/utils.php";

$search = get_required("search");
$offset = get_int("offset", 0);
$count = get_int("count", 10);

description("Функция ищет похожие домены");

$response["results"] = domain_get_list($search, $offset, $count);

echo json_encode_readable($response);