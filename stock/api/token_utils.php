<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$domains = [];

function save_token($domain, $index, $prev_key, $key_hash)
{
    $prev_key_hash = md5($prev_key);
    $domain_id = $GLOBALS[domains][$domain];
    if ($domain_id == null) {
        $coin = selectRowWhere(coins, [domain => $domain]);
        $domain_id = $coin[domain_id];
        $GLOBALS[domains][$domain] = $domain_id;
    }
    $can_update = updateWhere(tokens, [archived => 1], [domain_id => $domain_id, index => $index, archived => 0, key_hash => $prev_key_hash]);
    if ($can_update == true) {
        return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $prev_key, key_hash => $key_hash, time => time()]);
    } else {
        $last_token = selectRowWhere(tokens, [domain_id => $domain_id, index => $index, archived => 0]);
        if ($last_token == null) {
            return insertRow(tokens, [domain_id => $domain_id, index => $index, time => time()]);
        } else {
            if ($last_token[key_hash] == $prev_key_hash) {
                return insertRow(tokens, [domain_id => $domain_id, index => $index, prev_key => $prev_key, key_hash => $key_hash, time => time()]);
            } else {
                return false;
            }
        }
    }
}


function generate_token($domain, $supply)
{
    $domain_id = selectRowWhere(coins, [domain => $domain])[domain_id];
    $time = time();
    for ($i = 0; $i < $supply;) {
        $tokens = [];
        $package = 100;
        for ($j = 0; $j < $package && $i < $supply; $j++) {
            $key = "".random_id();
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

