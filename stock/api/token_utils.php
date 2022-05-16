<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

function hash_sha56($str)
{
    return hash("sha256", $str);
}

$domains = [];

function save_token($domain, $index, $prev_key, $key_hash) {
    $prev_key_hash = hash_sha56($prev_key);
    $domain_id = $GLOBALS[domains][$domain];
    if ($domain_id == null) {
        $domain_id = selectRowWhere(domains, [name => $domain]);
        $GLOBALS[domains][$domain] = $domain_id;
    }
    $can_update = updateWhere(tokens, [archived => 1], [domain_id => $domain_id, index => $index, archived => 0, key_hash => $prev_key_hash]);
    if ($can_update == true) {
        return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $prev_key, key_hash => $key_hash, time => microtime(true)]);
    } else {
        $last_token = selectRowWhere(tokens, [domain_id => $domain_id, index => $index, archived => 0]);
        if ($last_token == null) {
            return insertRow(tokens, [domain_id => $domain_id, index => $index, time => microtime(true)]);
        } else {
            if ($last_token[key_hash] == $prev_key_hash) {
                return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $prev_key, key_hash => $key_hash, time => microtime(true)]);
            } else {
                return false;
            }
        }
    }
}
