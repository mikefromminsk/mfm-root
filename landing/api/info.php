<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$_POST[domain] = getDomain();

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/profile.php";