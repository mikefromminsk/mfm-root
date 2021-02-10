<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

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