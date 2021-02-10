<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/domain_utils.php";

/*$login = get_required("login");
$password = get_required("password");*/

// test login

http_request("http://username:password@hostname:9090/path?arg=value#ancho", array(
    "asdf" => 1123
));

/*data_put("users.$login", "123");
$response = data_get("users.$login");*/

echo json_encode($response);