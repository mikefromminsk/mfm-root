<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/node/domain_utils.php";

$user = get_required("user");
$pass = get_required("pass");
$mode = get("mode");

if ($user == $db_user && $pass == $db_pass) {
    query("DROP TABLE IF EXISTS `domains`;");

    query("CREATE TABLE IF NOT EXISTS `domains` (
`domain_name` varchar(256) COLLATE utf8_bin NOT NULL,
  `domain_name_hash` int(11) NOT NULL,
  `domain_prev_key` varchar(128) COLLATE utf8_bin DEFAULT NULL,
  `domain_key_hash` varchar(64) CHARACTER SET utf8 NOT NULL,
  `server_repo_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `domain_set_time` int(11) NOT NULL,
  `server_group_id` bigint(14) NOT NULL,
  UNIQUE KEY `domain_name` (`domain_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");

    query("DROP TABLE IF EXISTS `files`;");
    query("CREATE TABLE IF NOT EXISTS `files` (
  `server_group_id` bigint(14) NOT NULL,
  `file_path` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `file_level` int(11) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_hash` varchar(64) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    query("DROP TABLE IF EXISTS `servers`;");
    query("CREATE TABLE IF NOT EXISTS `servers` (
  `server_group_id` bigint(14) NOT NULL,
  `server_host_name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `server_repo_hash` varchar(64) CHARACTER SET utf8 DEFAULT NULL,
  `server_set_time` int(11) NOT NULL,
  `server_sync_time` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

    mkdir("files");
    //scandir("files"); and remove all files

    if ($mode != null) {

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

                $server_group_id = domain_set($app_name, null, hash(HASH_ALGO, hash(HASH_ALGO, "init")), null);
                $app_files = file_list_rec($path, $ignore_list);

                foreach ($app_files as $app_file) {
                    $file_path = substr($app_file, strlen($_SERVER["DOCUMENT_ROOT"]) + 1);
                    $path_items = explode("/", $file_path);
                    $app_name = array_shift($path_items);
                    $file_path = implode("/", $path_items);

                    $hash = hash_file(HASH_ALGO, $app_file);

                    copy($app_file, $_SERVER["DOCUMENT_ROOT"] . "/node/files/" . $hash);

                    insertList("files", array(
                        "server_group_id" => $server_group_id,
                        "file_path" => $file_path,
                        "file_level" => substr_count($file_path, "/"),
                        "file_size" => filesize($app_file),
                        "file_hash" => $hash,
                    ));
                }
                $server_repo_hash = hash(HASH_ALGO, domain_repo_get($server_group_id));
                $success = domain_set($app_name, "init", hash(HASH_ALGO, hash(HASH_ALGO, $apps_password) . $server_repo_hash), $server_repo_hash);
                if ($success === false)
                    error("doddd");

                update("update servers set server_repo_hash = '" . uencode($server_repo_hash) . "'"
                    . " where server_group_id = $server_group_id and server_host_name = '" . uencode($host_name) . "'");
            }
        }

        echo $apps_password;
    }
}
