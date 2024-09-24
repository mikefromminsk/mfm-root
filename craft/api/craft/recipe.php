<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$recipe = get_required(recipe);
$domain = getDomain();

foreach ($recipe as $component_domain => $amount) {
    dataSet([$domain, recipe, $component_domain], $amount);
    tokenScriptReg($component_domain, $domain . _craft, "craft/api/craft.php");
}

tokenScriptReg($domain, $domain . _craft, "craft/api/craft.php");

commit();