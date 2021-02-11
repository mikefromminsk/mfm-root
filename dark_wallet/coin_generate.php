<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_wallet/login.php";

$domain_name = get_required("domain_name");
$domain_postfix_length = get_int_required("domain_postfix_length");


if ($login != "admin") {
    // send to fiends
    /*foreach ($friends as $friend){
        http_post($friend . "/dark_wallet/coin_payment.php", array(

        ));
    }*/
}

$max_index = pow(10, $domain_postfix_length);
for ($i = 0; $i < $max_index % 1000; $i++) {
    $domains = array();
    for ($j = 0; $j < min(1000, $max_index); $j++) {
        $index = $i * 1000 + $j;
        $domain_postfix = sprintf("%.0" . $domain_postfix_length . "d", $index);
        $new_domain = $domain_name . $domain_postfix;
        $key = random_id();
        $domains[] = array(
            "domain_name" => $new_domain,
            "domain_prev_key" => null,
            "domain_key_hash" => hash_sha56($key),
            "server_repo_hash" => null,
        );
        data_put("users.$login.private.keys.$new_domain", $token, $key);
    }
    domains_set($host_name, $domains);
}
