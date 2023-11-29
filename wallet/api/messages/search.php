<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$search_text = get_required(search_text);

$response[result] = dataSearch([wallet, tokens], $search_text);

commit($response);