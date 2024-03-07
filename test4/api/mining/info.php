<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = getDomain();

$last_hash = dataGet([$domain, mining, last_hash]) ?: "";
$difficulty = dataGet([$domain, mining, difficulty]) ?: 1;

$response[last_hash] = $last_hash;
$response[difficulty] = $difficulty;

commit($response);