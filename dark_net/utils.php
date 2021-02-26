<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";


function http_json_put($url, $fields)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    $result = curl_exec($ch);
    curl_close($ch);
    return json_decode($result, true);
}

function to_utf8($mixed)
{
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value)
            $mixed[$key] = to_utf8($value);
    } elseif (is_string($mixed)) {
        return mb_convert_encoding($mixed, 'UTF-8', 'ISO-8859-1');
    }
    return $mixed;
}

function http_post($url, $data, $headers = array())
{
    if (strpos($url, "http://") === 0)
        $url = "http://" . $url;
    //if ($uencode)
    $data = to_utf8($data);
    $data_string = json_encode($data);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array_merge($headers, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
    ));
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function http_post_json($url, $data, $headers = array())
{
    $result = http_post($url, $data, $headers);
    return is_string($result) ? json_decode($result, true) : $result;
}

function http_get($url)
{
    if (strpos($url, "http://") === 0)
        $url = "http://" . $url;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function http_get_json($url)
{
    $result = http_get($url);
    return is_string($result) ? json_decode($result, true) : $result;
}

function redirect($url, $params = array(), $params_in_url = true)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($params_in_url == true) {
            $url_params = "";
            foreach ($params as $key => $value)
                $url_params .= "&" . urlencode($key) . "=" . urlencode($value);
            if (strpos($url, "?") === false && $url_params != "")
                $url_params[0] = "?";
            $url .= $url_params;
        }
        $redirect_script = '<html><body><form id="redirect" action="' . $url . '" method="post">';
        if ($params_in_url == false)
            foreach ($params as $key => $value)
                $redirect_script .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities(json_encode($value)) . '">';
        $redirect_script .= '</form><script>document.getElementById("redirect").submit();</script></body></html>';
        header("Content-type: text/html;charset=utf-8");
        header("Location: $url");
        die($redirect_script);
    }
}

function http_request($url, $params = array(), $headers = array())
{
    $url_scheme = parse_url($url);
    $route = selectRowWhere("routes", array(
        "route_protocol" => $url_scheme["scheme"] ?: "http",
        "route_address" => $url_scheme["host"] ?: "localhost",
    ));
    $result = null;
    if (doubleval($route["route_last_online_time"]) > doubleval($route["route_last_offline_time"])) {
        $result = http_post($url, $params, $headers);
    } else {
        // other host routes
    }

    if ($result === false) {
        updateWhere("routes", array("route_last_offline_time" => time()), array("route_id" => $route["route_id"]));
        // proxy request
    }
    if ($result !== false) {
        updateWhere("routes", array("route_last_online_time" => time()), array("route_id" => $route["route_id"]));
    }

    return $result;
}