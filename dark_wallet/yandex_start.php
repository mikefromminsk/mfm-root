<?php

$code = $_GET["code"];
if ($code == null)
    echo "code not found";

$data_string = http_build_query(array(
    "grant_type" => "authorization_code",
    "code" => $code,
    "client_id" => "AF4776FE50D78F573B2A28D82C9E28D2506753A98E97C090148B7A64C4816777",
    "client_secret" => "9405D57443BD0730FA83B2B4DC1FB12841E65D90F8A4A5CE793CE73B4D59AF3530F29E6DEE7E2AAE27AAD6B6E728F9982E2989D4D7D30D198610CEC3DF9B57CF",
), '', '&');
$ch = curl_init("https://money.yandex.ru/oauth/token");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/x-www-form-urlencoded',
    'Content-Length: ' . strlen($data_string)
));
$result = curl_exec($ch);
curl_close($ch);

$result = json_decode($result, true);

if ($result["access_token"] != null) {
    file_put_contents("yandex_token.php", "<?php die();\n" . $result["access_token"]);
    echo "success";
} else {
    echo $result;
}




