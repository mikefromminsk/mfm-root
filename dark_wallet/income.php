<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/login.php";

$keys = get_required("keys");

description("save tokens on server");

dataPush();