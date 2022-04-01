<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/stock0/auth.php";

$domain_name = get_required_uppercase("domain_name");
$postfix_length = get_required("postfix_length");
//$promo_url = get_required("promo_url");

description(basename(__FILE__));

if (dataGet(["coins", $domain_name], $pass) != null) error("domain name busy");


/*if ($promo_url != null) {
    $response = http_get_json($promo_url);
    foreach ($response["keys"] as $domain_name => $key)
        dataSet(["store", $response["domain_name"], $domain_name], $pass, $key);
    dataInc(["users", $login, "balance", $response["domain_name"]], $pass, sizeof($response["keys"]));
}*/

/*if ($domain_name == "HRP") */ {
    $response = domains_generate_keys($domain_name, $postfix_length);
    dataSet(["users", $login, "balance", $domain_name], $pass, sizeof($response));
    dataSet(["store", $domain_name], $pass, $response);
    if (dataGet(["users", $login, "balance", $domain_name], $pass) != sizeof($response)) error("admin didnt get hrp");
    if (dataCount(["store", $domain_name], $pass) != sizeof($response)) error("store dont have enough tokens");
}/* else {
    $month_price = 2;
    if (dataGet(["users", $login, "balance", "HRP"], $pass) < $month_price) error("not enough HRP");
    dataInc(["users", $login, "balance", "HRP"], $pass, $month_price);
    dataDec(["users", $login, "balance", "HRP"], $pass, $month_price);
    $response = domains_generate_keys($domain_name, $postfix_length);
    error(dataGet(["users", $login, "balance", "HRP"], $pass));
    dataSet(["store", $domain_name], $pass, $response);
    dataInc(["users", $login, "balance", $domain_name], $pass, sizeof($response));
    $response["count"] = dataCount(["users", $login, $domain_name], $pass);
    if (strlen($response["count"]) !== $postfix_length) error("not generated");
}*/


dataSet(["coins", $domain_name], $pass, array(
   "owner" => $login,
   "domain_name" => $domain_name,
   "postfix_length" => $postfix_length,
));

$response = [];
$response["added"] = dataGet(["users", $login, "balance", $domain_name], $pass);

echo json_encode($response);