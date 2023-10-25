<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/api/utils.php";

$developer_name = get_required(developer_name);

$last_commit_hash = dataGet([data, developers, $developer_name, last_commit_hash]);
$approved_commit_hash = voteApprove([data, developers, $developer_name, approvers]);

if ($approved_commit_hash != $last_commit_hash) {
    $from_address = dataGet([data, developers, $developer_name, from_address]);
    $developer_address = dataGet([data, developers, $developer_name, developer_address]);
    $portion = dataGet([data, developers, $developer_name, portion]);
    $response[result] = dataWalletSend([data, wallet], $from_address, $developer_address, $portion);
}

commit($response);

