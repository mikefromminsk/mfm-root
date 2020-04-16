<?php

include_once "db.php";

foreach (scandir(".") as $filename)
    if ($filename != "." && $filename != ".." && strpos($filename, ".php") !== false && $filename !== "properties.php")
        file_put_contents("../../host1/node/$filename", file_get_contents($filename));

http_get("localhost/node/init.php?user=root&pass=root&master=localhost");
http_get("localhost/node/commit.php?domain_name=node&domain_next_key=1");
http_get("localhost/node/commit.php?domain_name=node&domain_key=1&domain_next_key=2");
http_get("host1.com/node/init.php?user=root&pass=root&master=localhost");
echo http_get("host1.com/node/download.php?domain_name=node&server_host_name=localhost");