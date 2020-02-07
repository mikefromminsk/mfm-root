<?php

include_once "db.php";

$domains = get_required("domains");

$result = array(
    "input" => sizeof($domains),
    "exist" => 0,
    "inserted" => 0,
    "updated" => 0,
    "online" => 0,
    "errors" => 0,
);

// delete out of date domains
query("delete from domains where domain_last_online_time < " . (time() - (60 * 60 * 24 * 30))); // one month

foreach ($domains as $domain_remote) {
    $domain_name = uencode($domain_remote["domain_name"]);
    $request_result = false;
    $domain_local = selectMap("select * from domains where domain_name = '$domain_name'");
    if ($domain_local == null) {
        $request_result = insertList("domains", $domain_remote);
        $result["inserted"] += $request_result ? 1 : 0;
    } else {
        $result["exist"] += 1;
        if ($domain_remote["domain_next_hash"] == $domain_local["domain_next_hash"]) {
            $request_result = updateList("domains", array(
                "domain_last_online_time" => $domain_remote["domain_last_online_time"]
            ), "domain_name", $domain_name);
            $result["online"] += $request_result ? 1 : 0;
        } else {
            $domain_remote_prev_hash = hash("sha256", $domain_remote["domain_prev_name"]);
            if ($domain_local["domain_next_hash"] == $domain_remote_prev_hash) {
                $request_result = updateList("domains", $domain_remote, "domain_name", $domain_name);
                $result["updated"] += $request_result ? 1 : 0;
            }
        }
    }
    $result["errors"] += $request_result ? 0 : 1;
}

if ($result["input"] != $result["inserted"])
    $result["message"] = "inserted error";

echo json_encode($result);