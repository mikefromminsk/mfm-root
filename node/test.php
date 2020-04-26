<?php

include_once "db.php";

function sync_dir($dirname)
{
    foreach (scandir(".") as $filename)
        if ($filename != "." && $filename != ".." && strpos($filename, ".php") !== false && $filename !== "properties.php")
            file_put_contents("../../$dirname/node/$filename", file_get_contents($filename));
}

function assertEquals($message, $val, $need)
{
    if ($val != $need || $val == null)
        die("error $message val=$val need=$need");
    echo "good $message\n";
}

sync_dir("host1.com");
sync_dir("host2.com");
sync_dir("host3.com");

http_get("host1.com/node/init.php");
http_get("host1.com/node/commit.php?domain_name=node&domain_next_key=1");
http_get("host1.com/node/commit.php?domain_name=node&domain_key=1&domain_next_key=2");
http_get("host2.com/node/init.php");
http_get("host2.com/node/download.php?domain_name=node&server_host_name=host1.com");
http_get("host3.com/node/init.php");
http_get("host3.com/node/download.php?domain_name=node&server_host_name=host2.com");

assertEquals("test1", http_get_json("host1.com/node/scalar.php?sql=" . urlencode("select count(*) from domains")), 3);
assertEquals("test2", http_get_json("host2.com/node/scalar.php?sql=" . urlencode("select count(*) from domains")), 3);
assertEquals("test3", http_get_json("host3.com/node/scalar.php?sql=" . urlencode("select count(*) from domains")), 3);
