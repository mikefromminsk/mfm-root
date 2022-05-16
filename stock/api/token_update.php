<?php

include_once "token_utils.php";

$domain = get_string_required(domain);
$tokens = get_required(tokens);

$failed = [];
$updated = 0;
foreach ($tokens as $token) {
    if (save_token($domain, $token[index], $token[prev_key], $token[key_hash]))
        $updated += 1;
    else
        $failed[] = $token[name];
}

return [updated => $updated, failed => $failed];

