<?php

include_once "utils.php";

$domain_name = get_required("domain_name");
$domain_key = get_required("domain_key");
$domain_next_key = get_required("domain_next_key");

description("upload");

domain_set($host_name, $domain_name, $domain_key, hash_sha56($domain_next_key), "123");
