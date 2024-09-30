<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/world/api/utils.php";

$domain = get_required(domain);
$gas_address = get_required(gas_address);
$amount = get_int_required(amount);

$recipe = dataObject([recipe, $domain], 100);

foreach ($recipe as $component_domain => $component_amount) {
    worldSend($component_domain, [avatar, $gas_address], [world], $component_amount * $amount);
}

worldSend($domain, [world], [avatar, $gas_address], $amount);

commit();