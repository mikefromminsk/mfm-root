<?php

include_once "auth.php";
include_once "token_utils.php";

$file = get_required(file);

$package = json_decode(file_get_contents($file[tmp_name]), true);

$version = $package[version];
$domain = $package[domain];
$keys = $package[keys];

if ($version != 0.1) error("dont support this file version");
if ($domain == null) error("domain is empty");
if ($keys == null) error("keys is empty");


$coin = selectRowWhere(coins, [domain => $domain]);
if ($coin == null) error("tokens not support");


$deposited = 0;
$failed = [];
foreach ($keys as $key) {
    if (save_key($domain, $key[index], $key[key])){
        $deposited += 1;
    } else {
        $failed[] = $key[index];
    }
}

transfer(DEPOSIT, -1, $user_id, $coin[ticker], $deposited);

$response = [
  deposited => $deposited,
  failed => $failed,
];

echo json_encode($response);