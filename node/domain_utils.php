<?php

include_once "db.php";

define("HASH_ALGO", "sha256");

function domain_hash($domain_name, $fromIndex = 0)
{
    $charsum = 0;
    for ($i = $fromIndex; $i < strlen($domain_name); $i++)
        $charsum += ord($domain_name[$i]);
    return $charsum;
}

function domain_set($server_host_name, $new_domain)
{
    $time = microtime(true);

    $domain_key_hash = $new_domain["domain_prev_key"] != null ? hash(HASH_ALGO, $new_domain["domain_prev_key"]) : null;

    $updatePreviousResult = updateWhere("domains", array(
        "archived" => 1
    ), array(
        "domain_name" => $new_domain["domain_name"],
        "archived" => 0,
        "domain_key_hash" => $domain_key_hash,
    ));
    if ($updatePreviousResult == true) {
        insertRow("domains", array(
            "domain_name" => $new_domain["domain_name"],
            "domain_name_hash" => domain_hash($new_domain["domain_name"]),
            "domain_prev_key" => $new_domain["domain_prev_key"],
            "domain_key_hash" => $new_domain["domain_key_hash"],
            "server_repo_hash" => $new_domain["server_repo_hash"],
            "domain_set_time" => $time,
        ));
        return true;
    } else {
        $now_domain = selectRowWhere("domains", array(
            "domain_name" => $new_domain["domain_name"],
            "domain_key" => $new_domain["domain_prev_key"],
        ));
        if ($now_domain == null)
            return false;
        else {
            if ($new_domain["domain_key_hash"] != $now_domain["domain_key_hash"])
                consensus($server_host_name, $now_domain, $new_domain);
        }
        return $new_domain;
    }
}

function domains_set($server_host_name, $domains, $servers)
{
    $results = array();
    foreach ($domains as $domain) {
        $result = $results[$domain["domain_name"]];
        if ($result == null || $result == true)
            $results[$domain["domain_name"]] = domain_set($server_host_name, $domain);
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


    $response = array();
    foreach ($results as $result)
        if (is_array($result))
            $response = array_merge($response, selectWhere("domains", array(
                "domain_name" => $results["domain_name"],
                "domain_set_time >= " . $results["domain_set_time"],
            )));
    return $results;
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


function domain_get($domain_name)
{
    return;
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

function sync_request_data($server_host_name)
{
    $servers = select("select t1.* from servers t1 "
        . " left join domains t2 on t2.domain_name = t1.domain_name "
        . " where t1.server_host_name = '" . uencode($server_host_name) . "'"
        . " and t2.domain_set_time >= t1.server_sync_time");

    $domains_in_request = array();
    foreach ($servers as $server) {
        $domains_in_request = array_merge($domains_in_request,
            select("select * from domains where domain_name = '" . uencode($server["domain_name"]) . "' "
                . " and domain_set_time > " . $server["server_sync_time"]
                . " or domain_set_time = 0 "
                . " order by domain_set_time"));
    }
    return array(
        "server_host_name" => $GLOBALS["host_name"],
        "domains" => $domains_in_request,
        "servers" => selectMapList("select * from servers where domain_name in ('" . implode("','", array_column($servers, "domain_name")) . "')", "domain_name")
    );
}