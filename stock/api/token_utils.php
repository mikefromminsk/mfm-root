<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$domains = [];

function get_domain_id($domain){
    $domain_id = $GLOBALS[domains][$domain];
    if ($domain_id == null) {
        $coin = selectRowWhere(coins, [domain => $domain]);
        $domain_id = $coin[domain_id];
        $GLOBALS[domains][$domain] = $domain_id;
    }
    return $domain_id;
}

function save_token($domain, $index, $key, $next_key_hash)
{
    $key_hash = md5($key);
    $domain_id = get_domain_id($domain);
    $success_changed = updateWhere(tokens, [archived => 1], [domain_id => $domain_id, index => $index, archived => 0, key_hash => $key_hash]);
    if ($success_changed == true) {
        return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $key, key_hash => $next_key_hash, time => time()]);
    } else {
        $last_token = selectRowWhere(tokens, [domain_id => $domain_id, index => $index, archived => 0]);
        if ($last_token == null) {
            return insertRow(tokens, [domain_id => $domain_id, index => $index, time => time()]);
        } else {
            if ($last_token[key_hash] == $key_hash) {
                return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $key, key_hash => $next_key_hash, time => time()]);
            } else {
                return false;
            }
        }
    }
}

function save_key($domain, $index, $key)
{
    $next_key = "" . random_id();
    if (save_token($domain, $index, $key, md5($next_key))) {
        $domain_id = get_domain_id($domain);
        $exist_key = selectRowWhere(keys, [domain_id => $domain_id, index => $index]);
        if ($exist_key == null) {
            return insertRow(keys, [domain_id => $domain_id, index => $index, key => $next_key]);
        } else {
            return updateWhere(keys, [key => $next_key, archived => 0], [domain_id => $domain_id, index => $index]);
        }
    }
    return false;
}


function generate_token($domain, $supply)
{
    $domain_id = selectRowWhere(coins, [domain => $domain])[domain_id];
    $time = time();
    for ($i = 0; $i < $supply;) {
        $tokens = [];
        $package = 100;
        for ($j = 0; $j < $package && $i < $supply; $j++) {
            $key = "" . random_id();
            $tokens[] = [key => $key, index => $i, key_hash => md5($key)];
            $i++;
        }
        $query = "insert into tokens (`domain_id`, `index`, `key_hash`, `time`) values ";
        foreach ($tokens as $token)
            $query .= "($domain_id,$token[index],'$token[key_hash]',$time),";
        $query = rtrim($query, ",");
        insert($query);
        $query = "insert into `keys` (`domain_id`, `index`, `key`) values ";
        foreach ($tokens as $token)
            $query .= "($domain_id,$token[index],'$token[key]'),";
        $query = rtrim($query, ",");
        insert($query);
    }
}

