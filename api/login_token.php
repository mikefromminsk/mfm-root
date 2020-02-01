<?php

include_once "login.php";

echo json_encode(array("token" => $token));