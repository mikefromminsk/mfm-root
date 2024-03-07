<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);
$collection = get_required(collection);
$title = get_required(title);
$description = get_required(description);
$count = get_required(count);
$gas_address = get_required(gas_address);
$file = get_required(file);

$files = uploadContent($domain, $file[tmp_name], "nft/img");

$nft_hash = array_keys($files)[0];

$collection_hash = md5($collection);
dataSet([$domain, nft, collection, $collection_hash], [
    title => $title,
]);

dataNew([$domain, nft, collection, $collection_hash, items, $nft_hash], true);

dataSet([$domain, nft, item, $nft_hash], [
    collection => $collection,
    hash => $nft_hash,
    filename => $files[$nft_hash],
    title => $title,
    description => $description,
    owner => $gas_address,
    count => $count,
]);

$response[success] = true;

commit($response);