<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/dark_node/init.php";

$scalar = get("scalar");
if ($scalar != null)
    die(json_encode(scalar($scalar)));

function sync_dir($dirname)
{
    foreach (scandir(".") as $filename)
        if ($filename != "." && $filename != ".." && strpos($filename, ".php") !== false && $filename !== "properties.php")
            file_put_contents("../../$dirname/dark_node/$filename", file_get_contents($filename));
}

function assertEquals($message, $val, $need)
{
    if ($val != $need)
        die("error $message val=$val need=$need");
    echo "good $message\n";
}

sync_dir("host1.com");
sync_dir("host2.com");
sync_dir("host3.com");

http_get("host1.com/dark_node/init.php");
http_get("host1.com/dark_node/commit.php?domain_name=node&domain_next_key=1");
http_get("host1.com/dark_node/commit.php?domain_name=node&domain_key=1&domain_next_key=2");
assertEquals("test1", http_get_json("host1.com/dark_node/test.php?scalar=" . urlencode("select count(*) from domains")), 3);

http_get("host2.com/dark_node/init.php");
http_get("host2.com/dark_node/download.php?domain_name=node&server_host_name=host1.com");
assertEquals("test2", http_get_json("host2.com/dark_node/test.php?scalar=" . urlencode("select count(*) from domains")), 3);

http_get("host3.com/dark_node/init.php");
http_get("host3.com/dark_node/download.php?domain_name=node&server_host_name=host1.com");
assertEquals("test3", http_get_json("host3.com/dark_node/test.php?scalar=" . urlencode("select count(*) from domains")), 3);




http_get("host1.com/dark_node/upload.php?domain_name=node&domain_key=2&domain_next_key=3");
assertEquals("test5", http_get_json("host1.com/dark_node/test.php?scalar=" . urlencode("select domain_key_hash from domains where domain_name = 'node' and archived = 0")), hash_sha56("3"));

http_get("host2.com/dark_node/upload.php?domain_name=node&domain_key=2&domain_next_key=4");
assertEquals("test6", http_get_json("host2.com/dark_node/test.php?scalar=" . urlencode("select domain_key_hash from domains where domain_name = 'node' and archived = 0")), hash_sha56("4"));

http_get("host3.com/dark_node/upload.php?domain_name=node&domain_key=2&domain_next_key=4");
assertEquals("test7", http_get_json("host3.com/dark_node/test.php?scalar=" . urlencode("select domain_key_hash from domains where domain_name = 'node' and archived = 0")), hash_sha56("4"));

http_get("host1.com/dark_node/cron.php");
assertEquals("test8", http_get_json("host1.com/dark_node/test.php?scalar=" . urlencode("select domain_key_hash from domains where domain_name = 'node' and archived = 0")), hash_sha56("4"));
http_get("host2.com/dark_node/cron.php");
assertEquals("test9", http_get_json("host2.com/dark_node/test.php?scalar=" . urlencode("select error_key_hash from servers where domain_name = 'node' and server_host_name = 'host1.com'")), null);
http_get("host3.com/dark_node/cron.php");
assertEquals("test10", http_get_json("host3.com/dark_node/test.php?scalar=" . urlencode("select error_key_hash from servers where domain_name = 'node' and server_host_name = 'host1.com'")), null);

http_get("host2.com/dark_node/commit.php?domain_name=node&domain_key=4&domain_next_key=5");
echo http_get("host2.com/dark_node/cron.php");
assertEquals("test10", http_get_json("host2.com/dark_node/test.php?scalar=" . urlencode("select error_key_hash from servers where domain_name = 'node' and server_host_name = 'host1.com'")), null);
