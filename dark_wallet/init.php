<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/utils.php";

data_put("users.admin.private", $admin_token, array(
    "token" => $admin_token
));

data_put("tokens", $admin_token, array());