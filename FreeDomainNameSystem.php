<?php

$name = get("name");
$next_name = get("next_name");
$next_hash = get("next_hash", $next_name == null ? null : sha1($next_name));
$next_next_name = get("next_next_name");
$next_next_hash = get("next_next_hash", $next_next_name == null ? null : sha1($next_next_name));
$location = get("location");
$path = get("path", []);
$this_script_url = "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["SCRIPT_NAME"];

if ($name != null) {
    $result = array(
        "success" => false,
        "name" => $name,
        "domain" => null,
        "similarDomains" => [],
        "location" => $location,
        "next_name" => $next_next_hash != null ? null : $next_name,
        "next_hash" => $next_next_hash != null ? null : $next_hash,
        "next_next_hash" => $next_next_hash,
        "path" => $path,
    );

    if ($next_name != null && $next_next_hash == null && $location == null) {
        // update
        $result["success"] = updateList("domains", array(
            "online_time" => time()
        ), "name", $name);
    }

    // get
    $domains = select("select * from domains "
        . " where length(name) >= " . (strlen($name) - 1)
        . " and length(name) <= " . (strlen($name) + 1)
        . " and location not in ('" . implode('\'', $path) . "')");

    $result["path"][] = $this_script_url;

    function compare($first, $second)
    {
        $first = str_split($first);
        sort($first);
        $first = implode('', $first);

        $second = str_split($second);
        sort($second);
        $second = implode('', $second);

        $differenceLevel = 0;
        $secondIndex = 0;
        for ($firstIndex = 0; $firstIndex < strlen($first); $firstIndex++) {
            if ($first[$firstIndex] == $second[$secondIndex]) {
                $secondIndex++;
            } else if ($first[$firstIndex] < $second[$secondIndex]) {
                $differenceLevel += 1;
            } else if ($first[$firstIndex] > $second[$secondIndex]) {
                $differenceLevel += 1;
                $firstIndex--;
                $secondIndex++;
            }
            if ($secondIndex >= strlen($second) && $firstIndex != strlen($first) - 1) {
                $differenceLevel += strlen($first) - $firstIndex;
                break;
            }
        }
        return $differenceLevel;
    }

    foreach ($domains as $domain) {
        $differenceLevel = compare($name, $domain["name"]);
        if ($differenceLevel == 0)
            $result["domain"] = $domain;
        if ($differenceLevel == 1)
            $result["similarDomains"][] = $domain;
    }

    if ($result["domain"] == null) {
        foreach ($domains as $domain) {
            $result = http_json_post($domain["location"], $result);
            if ($result["domain"] != null) {
                $new_domains = array_merge($result["similarDomains"], [$result["domain"]]);
                foreach ($new_domains as $new_domain)
                    if (scalar("select count(*) from domains where name = '" . $new_domain["name"] . "'") == 0) {
                        insertList("domains", array(
                            "name" => $new_domain["name"],
                            "next_hash" => $new_domain["next_hash"],
                            "location" => $new_domain["location"],
                            "online_time" => $new_domain["online_time"]));
                    } else {
                        updateList("domains", array(
                            "next_hash" => $new_domain["next_hash"],
                            "location" => $new_domain["location"],
                            "online_time" => $new_domain["online_time"],
                        ), "name", $new_domain["name"]);
                    }
                break;
            }
        }
        if ($next_hash != null && $location != null && $next_next_hash == null && $result["domain"] == null) {
            // registration
            $result["success"] = insertList("domains", array(
                "name" => $name,
                "next_hash" => $next_hash,
                "location" => $location,
                "online_time" => time()
            ));
        }
    } else {
        if ($next_name != null && $next_hash != null && $next_next_hash != null && $next_hash != $next_next_hash
            && $location != null && $result["domain"]["next_hash"] == $next_hash) {
            // rename
            $result["success"] = updateList("domains", array(
                "name" => $name,
                "next_hash" => $next_next_hash,
                "location" => $location,
                "online_time" => time()
            ), "name", $name);

            $result["domain"] = selectMap("select * from domains where name = '$name'");
        }
    }
}

$domain_count = scalar("select count(*) from domains");

define("MAX_DOMAIN_LIVE_SECONDS", 60 * 20);
query("delete from domains where online_time < " . (time() - MAX_DOMAIN_LIVE_SECONDS));

if ($name != null) {
    $result["local_domain_count"] = $domain_count;
    die(json_encode_readable($result));
}


header("Content-Type: text/html; charset=utf-8");
$random = rand(0, 1000);
?>
<html>
    <body>
        This is FreeDomainNameSystem node ver 0.1 created by Mike Haiduk +375255451247<br>
        domain counts = <?=$domain_count?><br>
        Tests:<br>
        <a href="?name=domain<?=$random?>&next_name=next_domain<?=$random?>&location=<?=$this_script_url?>" target="_blank"><button>Registration</button></a><br>
        <a href="?name=domain<?=$random?>&next_name=next_domain<?=$random?>&location=<?=$this_script_url?>&next_next_name=next_next_domain<?=$random?>" target="_blank"><button>Rename</button></a><br>
        <a href="?name=domain<?=$random?>&next_name=next_domain<?=$random?>" target="_blank"><button>Update</button></a><br>
    </body>
</html>



