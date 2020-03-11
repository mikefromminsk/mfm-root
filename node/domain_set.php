<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$domain_name = get_required("domain_name");
$domain_key = get("domain_key");
$domain_key_hash = get("domain_key_hash");
$domain_repo_hash = get("domain_repo_hash");

if (!domain_set($domain_name, $domain_key, $domain_key_hash, $domain_repo_hash))
    error("domain is not set");
