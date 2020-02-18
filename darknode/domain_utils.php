<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

function domain_hash($domain_name, $fromIndex = 0)
{
    $charsum = 0;
    for ($i = $fromIndex; $i < strlen($domain_name); $i++)
        $charsum += ord($domain_name[$i]);
    return $charsum;
}

function domains_set($domain_prefix, $domains)
{
    $success_domain_changed = [];
    $server_url = $GLOBALS["server_url"];
    if ($server_url == null)
        error("server_url is not defined. Please check properties.php file");

    $current_server_item = selectMap("select * from servers where server_domain_name = '" . uencode($domain_prefix) . "' "
        . " and server_url = '" . uencode($server_url) . "'");

    if ($current_server_item != null) {
        $users_cache = array();
        foreach ($domains as $domain) {

            $user_id = null;
            $user_login = $domain["user_login"];
            $domain_name = $domain["domain_name"];

            if (strpos($domain_name, $domain_prefix) != 0)
                continue;

            if ($user_login != null) {
                if ($users_cache[$user_login] == null)
                    $users_cache[$user_login] = scalar("select user_id from users where user_login = '" . uencode($user_login) . "'");
                $user_id = $users_cache[$user_login];
            }

            $current_domain = selectMap("select * from domains where domain_name = '" . uencode($domain_name) . "'");
            if ($current_domain != null) {
                if (hash("sha256", $domain["domain_prev_key"]) == $current_domain["domain_next_key_hash"]) {
                    $domain_next_key = random_id();
                    $new_domain = array(
                        "domain_name" => $domain_name,
                        "domain_name_hash" => domain_hash($domain_name),
                        "domain_prev_key" => $domain["domain_prev_key"],
                        "domain_next_key_hash" => hash("sha256", $domain_next_key),
                        "domain_next_key" => $domain_next_key,
                        "server_group_id" => $current_server_item["server_group_id"],
                        "user_id" => $user_id,
                    );
                    if (updateList("domains", $new_domain, "domain_name", $domain_name))
                        $success_domain_changed[] = $new_domain["domain_name"];
                }
            } else {
                $new_domain = array(
                    "domain_name" => $domain_name,
                    "domain_name_hash" => domain_hash($domain_name),
                    "domain_prev_key" => $domain["domain_prev_key"],
                    "domain_next_key_hash" => $domain["domain_next_key_hash"],
                    "domain_next_key" => $domain["domain_next_key"],
                    "server_group_id" => $current_server_item["server_group_id"],
                    "user_id" => $user_id,
                );
                if (insertList("domains", $new_domain))
                    $success_domain_changed[] = $new_domain["domain_name"];
            }
        }

    } else
        error("domain name $domain_prefix is not hosting");

    return $success_domain_changed;
}


function getListFromStart($domain_prefix, $count, $user_id = null, $to_user_login = null)
{
    if ($user_id != null) {
        $where = "where user_id = $user_id and domain_name like '$domain_prefix%' limit $count";
        $domains = select("select domain_name, domain_next_key_hash, domain_next_key from domains $where");
        if ($user_id != null)
            update("update domains set user_id = null $where");
    } else {
        $domains = select("select domain_name, domain_next_key_hash from domains where domain_name like '$domain_prefix%' limit $count");
    }
    if ($to_user_login != null)
        foreach ($domains as $domain)
            $domain["user_login"] = $to_user_login;
    return $domains;
}
