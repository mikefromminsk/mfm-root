<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

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

function domain_set($domain_name, $domain_key, $domain_key_hash_next, $server_repo_hash)
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
            insertList("domains", $domain); // history
        } else {
            insertList("servers", array(
                "domain_name" => $domain_name,
                "server_host_name" => $GLOBALS["host_name"],
                "server_reg_time" => $time,
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

function domains_set($domains, $servers)
{
    $success_domains = [];
    foreach ($domains as $domain)
        if (domain_set($domain["domain_name"], $domain["domain_prev_key"], $domain["domain_key_hash"], $domain["server_repo_hash"]) !== false)
            $success_domains[] = $domain["domain_name"];

    foreach ($servers as $server) {
        if ($server["server_host_name"] != $GLOBALS["host_name"] && in_array($server["domain_name"], $success_domains)) {
            if (scalar("select count(*) from servers "
                    . " where domain_name = '" . uencode($server["domain_name"]) . "' "
                    . " and server_host_name = '" . uencode($server["server_host_name"]) . "'") == 0) {
                insertList("servers", array(
                    "domain_name" => $server["domain_name"],
                    "server_host_name" => $server["server_host_name"],
                    "server_repo_hash" => $server["server_repo_hash"],
                ));
            } else if ($server["server_repo_hash"] != null) {
                updateList("servers", array(
                    "server_repo_hash" => $server["server_repo_hash"]
                ), array(
                    "domain_name" => $server["domain_name"],
                    "server_host_name" => $server["server_host_name"]
                ));
            }
        }
    }
    return $success_domains;
}

function servers_get($domain_names)
{
    return select("select * from servers where domain_name in ('" . implode("','", $domain_names) . "')");
}

function domain_get($domain_name)
{
    return selectMap("select * from domains where domain_name = '" . uencode($domain_name) . "'");
}

function domain_similar($domain_name)
{
    $domain_name_hash = domain_hash($domain_name);
    return select("select * from domains "
        . " where domain_name_hash > " . ($domain_name_hash - 32768) . " and domain_name_hash < " . ($domain_name_hash + 32768)
        . " order by ABS(domain_name_hash - $domain_name_hash)  limit 5");
}

function domain_repo_get($domain_name)
{
    $files = select("select * from files where domain_name = '" . uencode($domain_name) . "' order by file_path");
    $repo = array();
    foreach ($files as $file)
        $repo[$file["file_path"]] = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/" . $file["domain_name"] . "/" . $file["file_path"]);
    return sizeof($repo) == 0 ? null : json_encode(to_utf8($repo));
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
        update("update servers set server_repo_hash = '" . uencode(hash_file(HASH_ALGO, $repo_path)) . "'"
            . " where domain_name = '" . uencode($domain_name) . "' and server_host_name = '" . uencode($GLOBALS["host_name"]) . "' ");
    }
}

function get_mime_type($filename)
{
    $idx = explode('.', $filename);
    $count_explode = count($idx);
    $idx = strtolower($idx[$count_explode - 1]);

    $mimet = array(
        'txt' => 'text/plain',
        'htm' => 'text/html',
        'html' => 'text/html',
        'php' => 'text/html',
        'css' => 'text/css',
        'js' => 'application/javascript',
        'json' => 'application/json',
        'xml' => 'application/xml',
        'swf' => 'application/x-shockwave-flash',
        'flv' => 'video/x-flv',

        // images
        'png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml',

        // archives
        'zip' => 'application/zip',
        'rar' => 'application/x-rar-compressed',
        'exe' => 'application/x-msdownload',
        'msi' => 'application/x-msdownload',
        'cab' => 'application/vnd.ms-cab-compressed',

        // audio/video
        'mp3' => 'audio/mpeg',
        'qt' => 'video/quicktime',
        'mov' => 'video/quicktime',

        // adobe
        'pdf' => 'application/pdf',
        'psd' => 'image/vnd.adobe.photoshop',
        'ai' => 'application/postscript',
        'eps' => 'application/postscript',
        'ps' => 'application/postscript',

        // ms office
        'doc' => 'application/msword',
        'rtf' => 'application/rtf',
        'xls' => 'application/vnd.ms-excel',
        'ppt' => 'application/vnd.ms-powerpoint',
        'docx' => 'application/msword',
        'xlsx' => 'application/vnd.ms-excel',
        'pptx' => 'application/vnd.ms-powerpoint',


        // open office
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    if (isset($mimet[$idx])) {
        return $mimet[$idx];
    } else {
        return 'application/octet-stream';
    }
}
