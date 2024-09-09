<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$address = get_required(address);
$domain = get_required(domain);
$domain1 = get_required(domain1);
$pass1 = get_required(pass1);
$domain2 = get_required(domain2);
$pass2 = get_required(pass2);

craft2($address, $domain, $domain1, $pass1, $domain2, $pass2);
commit();