<?php

include_once "login.php";

$delete_offer_id = get_int_required("delete_offer_id");

if ($delete_offer_id != null) {
    $delete_offer = selectMap("select * from offers where offer_id = $delete_offer_id");
    if ($delete_offer != null && $delete_offer["user_id"] == $user_id) {
        $request = array(
            "back_user_login" => $delete_offer["back_user_login"],
            "coin_code" => $delete_offer["have_coin_code"],
            "domain_keys" => getDomainKeys($delete_offer["user_id"], $delete_offer["have_coin_code"], $delete_offer["have_coin_count"]),
        );
        $response = http_json_post($delete_offer["back_host_url"], $request);
        $message = query("delete from offers where offer_id = $delete_offer_id") ? null : "delete offer error";
    }
}

echo json_encode(array("message" => $message));