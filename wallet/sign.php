<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$keys_file = get_required(keys);

$files = json_decode(file_get_contents($keys_file[tmp_name]), true);

$response = [];

foreach ($files as $file) {
    $stored_key = selectRowWhere(keys, [
        path => $file[path]
    ]);
    if ($stored_key == null) {
        $key = "" . random_id();
        $stored_key = [
            path => $file[path],
            key_hash => hash('sha256', $key),
            key => $key,
        ];
        insertRow(keys, $stored_key);
    }
    if ($file[key_hash] == null)
        $file[key_hash] = $stored_key[key_hash];
    else
        $file[key] = $stored_key[key];
    $response[] = $file;
}

header("Content-Disposition: attachment; filename=sign_keys.json");
echo json_encode($response);