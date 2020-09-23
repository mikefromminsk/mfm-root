<?php

function yandex($method, $params = array()) {
    $file =  file_get_contents("yandex_token.php");
    $access_token = explode("\n", $file)[1];
    if ($access_token == null)
        die("yandex access token is null. go to /wallet/api/yandex_authorize.html");
    $data_string = http_build_query($params);
    $ch = curl_init("https://money.yandex.ru/api/$method");
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Authorization: Bearer $access_token",
        "Content-Type: application/x-www-form-urlencoded",
        "Content-Length: " . strlen($data_string)
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    $result = json_decode($result, true);
    if ($result == null)
        die("request error: $method $access_token $data_string  " . json_encode($result));
    return $result;
}


