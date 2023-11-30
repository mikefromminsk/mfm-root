<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/properties.php";
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$fcm_server_key = get_required(fcm_server_key);

http_post("https://fcm.googleapis.com/fcm/send", [
    to => "dZUPu9JASSW2WN5sA99Cex:APA91bFE2hqMOkHoZdHkXNvUzMayYUPxUdnERe1BCN20uy81eroCPSpjbzc7cM5zPMwQU92uP3nubSN20Zl2Z-QRbYcAyOhBGaA0JRmgpfApZBKBegpyz7UYWA_1pf63nolh6zief1ex",
    notification => [
        title => "wefw",
        body => "wefwffff",
    ],
], [
    Authorization => "Bearer $fcm_server_key"
]);