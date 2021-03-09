<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/test.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/init.php";

requestEquals("localhost/paincoin/pain.php",
    array(
        "pain_text" => "I am bad person",
        "email" => "x29a100@mail.ru",
    ), "added", true);
