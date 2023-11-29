<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$address = get_required(address);

$response[dialogs] = dataKeys([wallet, dialogs, $address]);

if (dataExist([wallet, dialogs, support]))
    $response[dialogs][] = "support";

commit($response);