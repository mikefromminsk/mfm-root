<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/mail.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_net/telegram.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/paincoin/utils.php";

$pain_text = get_required("pain_text");
$email = get_required("email");

description("input pain message");

$promo = random_id();



