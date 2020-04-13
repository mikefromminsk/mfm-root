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

function domain_check($domain_name, $domain_key)
{
    $domain = domain_get($domain_name);
    if ($domain != null) {
        /*if ($domain_key_hash == $domain_key_hash_next)
                return false;*/
        if ($domain["domain_key_hash"] == null)
            return $domain;
        if ($domain["domain_key_hash"] != hash(HASH_ALGO, $domain_key))
            return false;
    }
    return $domain;
}

function domain_set($domain_name, $domain_key, $domain_key_hash_next, $server_repo_hash, $server_host = null)
{
    if ($domain_key_hash_next == null)
        return false;

    $domain = domain_check($domain_name, $domain_key);
    file_put_contents("domain_Set", json_encode_readable($domain));
    if ($domain !== false) {
        $time = microtime(true);
        if ($domain != null) {
            updateList("domains", array("archived" => 1), array("domain_name" => $domain_name, "archived" => 0));
            insertList("domains", array(
                "domain_name" => $domain_name,
                "domain_name_hash" => domain_hash($domain_name),
                "domain_prev_key" => $domain_key,
                "domain_key_hash" => $domain_key_hash_next,
                "server_repo_hash" => $server_repo_hash,
                "domain_set_time" => $time,
            ));
        } else {
            insertList("servers", array(
                "domain_name" => $domain_name,
                "server_host_name" => $GLOBALS["host_name"],
                "domain_key_hash" => $domain_key_hash_next,
            ));
            insertList("domains", array(
                "domain_name" => $domain_name,
                "domain_set_time" => 0,
                "archived" => 1,
            ));
            insertList("domains", array(
                "domain_name" => $domain_name,
                "domain_name_hash" => domain_hash($domain_name),
                "domain_key_hash" => $domain_key_hash_next,
                "server_repo_hash" => $server_repo_hash,
                "domain_set_time" => $time,
            ));
        }
        return true;
    }
    return false;
}

function domains_set($server_host_name, $domains, $servers)
{
    $results = array();
    foreach ($domains as $domain)
        if ($results[$domain["domain_name"] !== false]) {
            $results[$domain["domain_name"]] = domain_set($domain["domain_name"],
                $domain["domain_prev_key"],
                $domain["domain_key_hash"],
                $domain["server_repo_hash"],
                $server_host_name) === false ? $domain["domain_key_hash"] : true;
        }

    foreach ($results as $domain_name => $success_or_error_key_hash) {
        if ($success_or_error_key_hash === true) {
            $domain_key_hash = scalar("select domain_key_hash from domains where domain_name = '" . uencode($domain_name) . "' and archived = 0");
            foreach ($servers[$domain_name] as $server) {
                if ($server["domain_key_hash"] == $domain_key_hash) {
                    if (scalar("select count(*) from servers "
                            . " where domain_name = '" . uencode($server["domain_name"]) . "' "
                            . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
                        insertList("servers", array(
                            "domain_name" => $domain_name,
                            "server_host_name" => $server["server_host_name"],
                            "domain_key_hash" => $server["domain_key_hash"],
                            "server_repo_hash" => $server["server_repo_hash"],
                        ));
                    } else /*if ($server["server_repo_hash"] != null) */ {
                        updateList("servers", array(
                            "domain_error_key_hash" => null,
                            "domain_key_hash" => $server["domain_key_hash"],
                            "server_repo_hash" => $server["server_repo_hash"]
                        ), array(
                            "domain_name" => $domain_name,
                            "server_host_name" => $server["server_host_name"]
                        ));
                    }
                }
            }
        } else {
            updateList("servers", array(
                "domain_error_key_hash" => $success_or_error_key_hash
            ), array(
                "domain_name" => $domain_name,
                "server_host_name" => $server_host_name,
                "domain_error_key_hash" => null,
            ));
        }

    }

    return $results;
}

function domain_get($domain_name)
{
    return selectRow("select * from domains where domain_name = '" . uencode($domain_name) . "' and archived = 0");
}

function domain_repo_set($domain_name, $repo_path)
{
    $zip = new ZipArchive();
    if ($zip->open($repo_path) == TRUE) {
        for ($i = 0; $i < $zip->numFiles; $i++)
            file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/$domain_name/" . $zip->getNameIndex($i), $zip->getFromName($zip->getNameIndex($i)));
        updateList("servers", array("server_repo_hash" => hash_file(HASH_ALGO, $repo_path)),
            array("domain_name" => $domain_name, "server_host_name" => $GLOBALS["host_name"]));
    }
}

function upgrade($domain_name)
{
    $active_server_repo_hash = domain_get($domain_name)["server_repo_hash"];

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