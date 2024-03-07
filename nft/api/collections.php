<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$domain = get_required(domain);

$collection_hashes = dataKeys([$domain, nft, collection]);
$collections = [];
foreach ($collection_hashes as $collection_hash) {
    $items_hashes = dataKeys([$domain, nft, collection, $collection_hash, items]);
    $items = [];
    foreach ($items_hashes as $item_hash){
        $items[] = [
            title => dataGet([$domain, nft, item, $item_hash, title]),
            description => dataGet([$domain, nft, item, $item_hash, description]),
            count => dataGet([$domain, nft, item, $item_hash, count]),
            owner => dataGet([$domain, nft, item, $item_hash, owner]),
            filename => dataGet([$domain, nft, item, $item_hash, filename]),
            collection => dataGet([$domain, nft, item, $item_hash, collection]),
            hash => $item_hash,
        ];
    }
    $collections[] = [
        title => dataGet([$domain, nft, collection, $collection_hash, title]),
        items => $items,
    ];
}

commit($collections);