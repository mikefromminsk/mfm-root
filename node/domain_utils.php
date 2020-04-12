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
        $domain_key_hash = hash(HASH_ALGO, $domain_key);
        if ($domain_key_hash != $domain["domain_key_hash"])
            return false;
    }
    return $domain;
}

function domain_set($domain_name, $domain_key, $domain_key_hash_next, $server_repo_hash, $server_host = null)
{
    if ($domain_key_hash_next == null)
        return false;

    $domain = domain_check($domain_name, $domain_key);

    if ($domain !== false) {
        $time = time();
        if ($domain != null) {
            $domain_set_time = $domain["domain_set_time"] >= $time ? $domain["domain_set_time"] + 1 : $time;
            updateList("domains", array(
                "domain_prev_key" => $domain_key,
                "domain_key_hash" => $domain_key_hash_next,
                "server_repo_hash" => $server_repo_hash,
                "domain_set_time" => $domain_set_time,
            ), "domain_name", $domain_name);
            $domain["domain_name"] .= "_" . substr(dechex((float)$domain["domain_set_time"]), 4);
            $domain["domain_name_hash"] = 0;
            insertList("domains", $domain); // history
        } else {
            insertList("servers", array(
                "domain_name" => $domain_name,
                "server_host_name" => $GLOBALS["host_name"],
                "domain_key_hash" => $domain_key_hash_next,
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
            $domain_key_hash = scalar("select domain_key_hash from domains where domain_name = '" . uencode($domain_name) . "'");
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
                    } else /*if ($server["server_repo_hash"] != null) */{
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
    return selectRow("select * from domains where domain_name = '" . uencode($domain_name) . "'");
}

function domain_similar($domain_name)
{
    $domain_name_hash = domain_hash($domain_name);
    return select("select * from domains "
        . " where domain_name_hash > " . ($domain_name_hash - 32768) . " and domain_name_hash < " . ($domain_name_hash + 32768)
        . " order by ABS(domain_name_hash - $domain_name_hash)  limit 5");
}

function domain_repo_set($domain_name, $repo_path)
{
    if ($GLOBALS["host_name"] == null)
        error("host_name is not set");
    $zip = new ZipArchive();
    if ($zip->open($repo_path) == TRUE) {
        $file_paths = selectList("select file_path from files where domain_name = '" . uencode($domain_name) . "'");
        query("delete from files where domain_name = '" . uencode($domain_name) . "'");
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $file_path = $zip->getNameIndex($i);
            $file_data = $zip->getFromName($file_path);
            if (file_put_contents($_SERVER["DOCUMENT_ROOT"] . "/$domain_name/$file_path", $file_data))
                unset($file_paths[array_search($file_path, $file_paths)]);
            $hash = hash(HASH_ALGO, $file_data);
            insertList("files", array(
                "domain_name" => $domain_name,
                "file_path" => $file_path,
                "file_level" => substr_count($file_path, "/"),
                "file_size" => strlen($file_data),
                "file_hash" => $hash,
            ));
        }
        foreach ($file_paths as $file_path)
            unlink($_SERVER["DOCUMENT_ROOT"] . "/$domain_name/$file_path");
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