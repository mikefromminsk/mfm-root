<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

$token = get_required("token");

$login = data_get("tokens.$token", $admin_token);

