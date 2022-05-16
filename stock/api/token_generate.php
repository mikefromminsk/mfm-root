<?php

include_once "token_utils.php";

$domain = get_int_required(domain);
$supply = get_int_required(supply);

$domain_id = insertRowAndGetId(domains, [domain => $domain]);

for ($i = 0; $i < $supply; $i++) {
    $key = random_id();
    save_token($domain, $i, null, hash_sha56($key));
    insertRow(keys, [domain_id => $domain_id, index => $i, key => $key]);
}
