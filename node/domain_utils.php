<?php

include_once "db.php";

define("HASH_ALGO", "sha256");

function hash_sha56($string)
{
    return $string == null ? null : hash(HASH_ALGO, $string);
}

function domain_hash($domain_name, $fromIndex = 0)
{
    $charsum = 0;
    for ($i = $fromIndex; $i < strlen($domain_name); $i++)
        $charsum += ord($domain_name[$i]);
    return $charsum;
}

function domain_set($server_host_name, $new_domain)
{
    $domain_name = $new_domain["domain_name"];

    $domain_prev_key_hash = hash_sha56($new_domain["domain_prev_key"]);
    $updatePreviousResult = updateWhere("domains", array(
        "archived" => 1
    ), array(
        "domain_name" => $domain_name,
        "archived" => 0,
        "domain_key_hash" => $domain_prev_key_hash,
    ));

    if ($updatePreviousResult == true) {
        insertRow("domains", array(
            "domain_name" => $domain_name,
            "domain_name_hash" => domain_hash($domain_name),
            "domain_prev_key" => $new_domain["domain_prev_key"],
            "domain_key_hash" => $new_domain["domain_key_hash"],
            "server_repo_hash" => $new_domain["server_repo_hash"],
            "domain_set_time" => microtime(true),
        ));
        return true;
    } else {
        $now_domain = selectRowWhere("domains", array(
            "domain_name" => $domain_name,
            "domain_key_hash" => $domain_prev_key_hash,
        ));
        if ($now_domain == null) {
            $prev_domain = scalarWhere("domains", "count(*)", array("domain_name" => $domain_name));
            if ($prev_domain == 0) {
                insertRow("domains", array(
                    "domain_name" => $domain_name,
                    "domain_set_time" => microtime(true),
                    "archived" => 1,
                ));
                insertRow("domains", array(
                    "domain_name" => $domain_name,
                    "domain_name_hash" => domain_hash($domain_name),
                    "domain_key_hash" => $new_domain["domain_key_hash"],
                    "server_repo_hash" => $new_domain["server_repo_hash"],
                    "domain_set_time" => microtime(true),
                ));
                return true;
            }
            return false;
        } else {
            if ($new_domain["domain_key_hash"] != $now_domain["domain_key_hash"])
                consensus($server_host_name, $now_domain, $new_domain);
        }
        return $now_domain;
    }
}

function domains_set($server_host_name, $domains, $servers)
{
    $results = array();
    foreach ($domains as $domain) {
        $result = $results[$domain["domain_name"]];
        if ($result == null || $result == true) {
            $results[$domain["domain_name"]] = domain_set($server_host_name, $domain);
        }
    }

    // add valid servers
    foreach ($results as $domain_name => $result)
        if ($result == true) {
            foreach ($servers[$domain_name] as $server_new) {
                $server_now = selectRowWhere("servers", array(
                    "domain_name" => $domain_name,
                    "server_host_name" => $server_new["server_host_name"],
                ));
                if ($server_now == null) {
                    insertRow("servers", array(
                        "domain_name" => $domain_name,
                        "server_host_name" => $server_new["server_host_name"],
                        "domain_key_hash" => $server_new["domain_key_hash"],
                        "server_repo_hash" => $server_new["server_repo_hash"],
                    ));
                } else {
                    if ($server_now["error_key_hash"] == null) {
                        updateWhere("servers", array(
                            "domain_key_hash" => $server_new["domain_key_hash"],
                            "server_repo_hash" => $server_new["server_repo_hash"]
                        ), array(
                            "domain_name" => $domain_name,
                            "server_host_name" => $server_new["server_host_name"]
                        ));
                    } else {

                    }
                }
            }
        }


    $response_domains = array();
    foreach ($results as $domain_name => $result)
        if (is_array($result)) {
            $response_domains = array_merge($response_domains, selectWhere("domains", array(
                "domain_name" => $result["domain_name"],
                "domain_set_time > " . $result["domain_set_time"],
            )));
        }
    return $response_domains;
}

function consensus($server_host_name, $now_domain, $new_domain)
{
    updateWhere("servers", array(
        "error_key_hash" => $new_domain["domain_key_hash"],
    ), array(
        "domain_name" => $now_domain["domain_name"],
        "server_host_name" => $server_host_name,
    ));
    $main_key_hash = scalar("select error_key_hash, sum(server_ping) as ping_sum from servers "
        . " where domain_name = '" . uencode($now_domain["domain_name"]) . "'"
        . " group by error_key_hash"
        . " order by ping_sum"
        . " limit 1");
    if ($main_key_hash != null) { // change branch
        updateWhere("servers", array(
            "error_key_hash" => $now_domain["domain_key_hash"]
        ), array("error_key_hash" => null));
        updateWhere("servers", array(
            "error_key_hash" => null
        ), array("error_key_hash" => $new_domain["domain_key_hash"]));
        if ($now_domain["archived"] == 1)
            query("delete from domains where domina_set_time > " . $now_domain["domain_set_time"]);
        updateWhere("domains", array(
            "domain_key_hash" => $main_key_hash,
            "archived" => 0,
            "domain_set_time" => microtime(true),
        ), array(
            "domain_name" => $now_domain["domain_name"],
            "domain_key_hash" => $now_domain["domain_key_hash"],
        ));
    }
}


function domain_repo_set($domain_name, $repo_path)
{
    $zip = new ZipArchive();
    if ($zip->open($repo_path) == TRUE) {
        for ($i = 0; $i < $zip->numFiles; $i++)
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/$domain_name/" . $zip->getNameIndex($i), $zip->getFromName($zip->getNameIndex($i)));
        updateWhere("servers", array("server_repo_hash" => hash_file(HASH_ALGO, $repo_path)),
            array("domain_name" => $domain_name, "server_host_name" => $GLOBALS["host_name"]));
    }
}

function upgrade($domain_name)
{
    $active_server_repo_hash = scalar("select server_repo_hash from domains where domain_name = '" . uencode($domain_name) . "' and archived = 0");

    $self_server_repo_hash = scalar("select server_repo_hash from servers "
        . " where domain_name = '" . uencode($domain_name) . "' "
        . " and server_host_name = '" . uencode($GLOBALS["host_name"]) . "'");

    if ($self_server_repo_hash != $active_server_repo_hash) {
        $server_host_name = scalar("select server_host_name from servers "
            . " where domain_name = '" . uencode($domain_name) . "' "
            . " and server_repo_hash = '" . uencode($active_server_repo_hash) . "' limit 1");

        $repo_string = http_get("$server_host_name/$domain_name/app.zip");
        $repo_path = $_SERVER["DOCUMENT_ROOT"] . "/$domain_name/app.zip";
        file_put_contents($repo_path, $repo_string);
        domain_repo_set($domain_name, $repo_path);
    }
}

function servers($domain_names)
{
    return selectMapList("select * from servers where domain_name in ('" . implode("','", $domain_names) . "')", "domain_name");
}

function sync_request_data($server_host_name)
{

}