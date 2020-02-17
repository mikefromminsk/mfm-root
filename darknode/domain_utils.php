<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

function domain_hash($domain_name)
{
    $charsum = 0;
    for ($i = 0; $i < strlen($domain_name); $i++)
        $charsum += ord($domain_name[$i]);
    return $charsum;
}

function domains_set($domain_prefix, $domains, $servers)
{
    $success_domain_changed = [];
    $server_url = $GLOBALS["server_url"];
    if ($server_url == null)
        return "server_url is not defined";

    $current_server_item = selectMap("select * from servers where server_domain_name = '" . uencode($domain_prefix) . "' "
        . " and server_url = '" . uencode($server_url) . "'");
    if ($current_server_item != null) {

        foreach ($domains as $domain) {
            /*if (strlen($domain_prefix["domain_name"]) == strlen($domain_prefix + 1)
                && substr($domain["domain_name"], 0, strlen($domain_prefix)) == $domain_prefix)*/ {

                $current_domain = selectMap("select * from domains where domain_name = '" . uencode($domain["domain_name"]) . "'");
                if ($current_domain != null) {
                    if (hash("sha256", $domain["domain_prev_key"]) == $current_domain["domain_next_key_hash"]) {
                        $domain_next_key = random_id();
                        $new_domain = array(
                            "domain_name" => $domain["domain_name"],
                            "domain_name_hash" => domain_hash($domain["domain_name"]),
                            "domain_prev_key" => $domain["domain_prev_key"],
                            "domain_next_key_hash" => hash("sha256", $domain_next_key),
                            "domain_next_key" => $domain_next_key,
                            "server_group_id" => $current_server_item["server_group_id"],
                        );
                        if (updateList("domains", $new_domain, "domain_name", $domain["domain_name"]))
                            $success_domain_changed[] = $new_domain["domain_name"];
                    }
                } else {
                    $new_domain = array(
                        "domain_name" => $domain["domain_name"],
                        "domain_name_hash" => domain_hash($domain["domain_name"]),
                        "domain_prev_key" => $domain["domain_prev_key"],
                        "domain_next_key_hash" => $domain["domain_next_key_hash"],
                        "domain_next_key" => $domain["domain_next_key"],
                        "server_group_id" => $current_server_item["server_group_id"],
                    );
                    if (insertList("domains", $new_domain))
                        $success_domain_changed[] = $new_domain["domain_name"];
                }
            }
        }

        /*foreach ($servers as $server) {
            if ($server["server_domain_name"] == $domain_prefix && $server["server_url"] != $server_url) {
                $server_in_db = selectMap("select * from servers where server_domain_name = '" . uencode($server["server_domain_name"]) . "'"
                    . " and server_url = '" . uencode($server["server_url"]) . "'");
                if ($server_in_db == null) {
                    insertList("servers", array(
                        "server_group_id" => $current_server_item["server_group_id"],
                        "server_domain_name" => $server["server_domain_name"],
                        "server_url" => $server["server_url"],
                    ));
                }
            }
        }*/

    } else {
        return "domain name $domain_prefix is not hosting";
    }

    return $success_domain_changed;
}