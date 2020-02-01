<?php

include_once "login.php";

$coin_code = get_required("coin_code");
$coin_count = get_int_required("coin_count");
$receiver_user_login = get_required("receiver_user_login");
$message = null;

$receiver = selectMap("select * from users where user_login = '$receiver_user_login'");
if ($receiver != null) {
    if ($receiver["user_id"] != $user["user_id"]) {

        $domain_names = selectList("select domain_name from domain_keys where user_id = $user_id and coin_code = '$coin_code' limit $coin_count");
        if (sizeof($domain_names) == $coin_count) {

            foreach ($domain_names as $index => $domain_name)
                $domain_names[$index] = uencode($domain_name);
            update("update domain_keys set user_id = " . $receiver["user_id"]
                . " where user_id = $user_id and coin_code = '$coin_code' and domain_name in ('" . implode("','", $domain_names) . "')");

            $request_data = array("domains" => selectList("select * from domains where domain_name in ('" . implode("','", $domain_names) . "')"));
            $node_locations = selectList("select distinct node_location from domains where node_location <> '$node_url' limit 5") ?: $start_node_locations;
            foreach ($node_locations as $node_location)
                http_json_post($node_location, $request_data);
        } else
            $message = "not enough coins";
    } else
        $message = "you cannot send coins to yourself";
} else
    $message = "receiver doesnt exist";

echo json_encode(array("message" => $message));