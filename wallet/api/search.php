<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$path = get_path_required(path);
$search_text = get_required(search_text);

$results = dataSearch($path, $search_text);

foreach ($results as $domain) {
    $response[result][] = [
            domain => $domain,
            path => dataGet([$path, $domain, path])
        ];
}
if ($response[result] == null) error("nothing found");

commit($response);