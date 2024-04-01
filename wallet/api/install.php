<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$app_domain = get_required(app_domain);

installApp($domain, $app_domain);
/*if ($domain != $app_domain && dataExist([wallet, info, $domain]))
    installApp($domain, $domain);*/

$response[success] = true;
commit($response);
