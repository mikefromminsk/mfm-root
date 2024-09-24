<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/craft/api/utils.php";

$domain = get_required(domain);

$response[recipe] = dataObject([$domain, recipe], 100);

commit($response);