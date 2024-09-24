<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$domain = get_required(domain);
$address = get_required(address);
$components = get_required(components);
$components = json_decode($components, true);

$recipe = dataObject([$domain, recipe], 100);

foreach ($recipe as $component_domain => $amount) {
    tokenSend($component_domain, $address, $domain . _craft, $amount, $components[$component_domain]);
}

tokenSend($domain, $domain . _craft, $address, 1);

commit();