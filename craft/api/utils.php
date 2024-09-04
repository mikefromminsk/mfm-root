<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/token/utils.php";

function sortDomains($array)
{
    sort($array);
    return implode("/", $array);
}

function recipe2($domain1, $domain2)
{
    $domain = getDomain();
    if (dataGet([$domain, recipe2]) != null) error("recipe2 already exists");
    dataSet([$domain, recipe2, domain1], $domain1);
    dataSet([$domain, recipe2, domain2], $domain2);
    tokenScriptReg($domain1, $domain . _craft2, "craft/api/craft2.php");
    tokenScriptReg($domain2, $domain . _craft2, "craft/api/craft2.php");
    tokenScriptReg($domain, $domain . _craft2, "craft/api/craft2.php");
}


function craft2($address, $domain, $domain1, $pass1, $domain2, $pass2)
{
    tokenSend($domain1, $address, $domain . _craft2, 1, $pass1);
    tokenSend($domain2, $address, $domain . _craft2, 1, $pass2);
    tokenSend($domain, $domain . _craft2, $address, 1);
}
