<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$user = get_required("user");
$pass = get_required("pass");
$domain_name = get("domain_name");
$domain_prev_key = get("domain_prev_key");
$domain_key_hash = get("domain_key_hash");
$server_host_name = get("server_host_name");

if ($user == $db_user && $pass == $db_pass) {
    query("DROP TABLE IF EXISTS `domains`;");

    query("CREATE TABLE IF NOT EXISTS `domains` (
`domain_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `domain_set_time` int(11) NOT NULL,
  `server_group_id` bigint(14) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `files`;");
    query("CREATE TABLE IF NOT EXISTS `files` (
`file_parent_id` int(11) DEFAULT NULL,
  `file_id` int(11) NOT NULL AUTO_INCREMENT,
  `file_name` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `file_data` varchar(72) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    query("DROP TABLE IF EXISTS `servers`;");
    query("CREATE TABLE IF NOT EXISTS `servers` (
`server_group_id` bigint(14) NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_reg_time` int(11) NOT NULL,
  `server_sync_tyme` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    mkdir("files");
    //scandir("files"); and remove all files
    function file_list_rec($dir, &$ignore_list, &$results = array())
    {
        $files = scandir($dir);
        foreach ($files as $key => $value) {
            $path = realpath($dir . "/" . $value);
            $path = str_replace("\\", "/", $path);
            $ignore = false;
            foreach ($ignore_list as $ignore_item)
                $ignore = $ignore || (strpos($path, $ignore_item) !== false);
            if ($ignore)
                continue;
            if (!is_dir($path)) {
                $results[] = $path;
            } else if ($value != "." && $value != "..") {
                file_list_rec($path, $ignore_list, $results);
            }
        }
        return $results;
    }

    $ignore_list = explode("\r\n", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/.gitignore"));
    $rootfiles = scandir($_SERVER["DOCUMENT_ROOT"]);

    $apps_password = random_id();

    foreach ($rootfiles as $app_name) {
        $path = $_SERVER["DOCUMENT_ROOT"] . "/" . $app_name;
        if (($app_name != "." && $app_name != "..") && !in_array($app_name, $ignore_list) && is_dir($path)) {

            domain_set($app_name, null, hash("sha256", $apps_password));
            $app_files = file_list_rec($path, $ignore_list);
            foreach ($app_files as $app_file) {
                $local_path = substr($app_file, strlen($_SERVER["DOCUMENT_ROOT"]) + 1);
                $path_items = explode("/", $local_path);
                $app_name = array_shift($path_items);
                $local_path = implode("/", $path_items);

                $filemeta = file_get($app_name, $local_path, true);
                $hash = hash_file(HASH_ALGO, $app_file);
                $file_size_hex = sprintf("%0" . FILE_SIZE_HEX_LENGTH . "X", filesize($app_file));

                copy($app_file, $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash);
                updateList("files", array(
                    "file_data" => $file_size_hex . $hash
                ), "file_id", $filemeta["file_id"]);

            }
        }
    }

    if ($domain_name != null && $domain_prev_key != null && $domain_key_hash != null && $server_host_name != null) {
        $server_group_id = random_id();
        http_json_post($server_host_name, array(
            "domains" => [array(
                "domain_name" => $domain_name,
                "domain_prev_key" => $domain_prev_key,
                "domain_key_hash" => $domain_key_hash,
                "server_group_id" => $server_group_id,
            )],
            "servers" => [array(
                "server_group_id" => $server_group_id,
                "server_host_name" => $server_host_name,
            )]
        ));
    }

}
