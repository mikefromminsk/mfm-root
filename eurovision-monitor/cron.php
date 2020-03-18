<?php

require $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

$videos = array(
    "V_hgYnwZR8I" => ["country_code" => "bg", "name" => "Victoria - Tears Getting Sober"],
    "O9GAfFHZE-E" => ["country_code" => "ch", "name" => "Gjon's Tears - Répondez-moi"],
    "FxPm-Wz8qpY" => ["country_code" => "lt", "name" => "The Roop - On Fire"],
    "L_dWvTCdDQ4" => ["country_code" => "ru", "name" => "Little Big - Uno"],
    "1HU7ocv3S2o" => ["country_code" => "is", "name" => "Daði & Gagnamagnið - Think About Things"],
);

$response = http_json_get("https://www.googleapis.com/youtube/v3/videos?part=statistics&key=AIzaSyDS4NcpuaRv82o3sce2QSejsn6Xa-YeC8w"
    . "&id=" . implode(",", array_keys($videos)));

foreach ($response["items"] as $youtube_video) {
    $video_id = $youtube_video["id"];
    $video = $videos[$video_id];
    if (scalar("select count(*) from videos where video_id = '" . uencode($video_id) . "'") == 0) {
        insertList("videos", array(
            "video_id" => $video_id,
            "video_country_code" => $video["country_code"],
            "video_name" => $video["name"],
            "video_likes" => $youtube_video["statistics"]["likeCount"],
            "video_dislikes" => $youtube_video["statistics"]["dislikeCount"],
            "video_views" => $youtube_video["statistics"]["viewCount"],
        ));
    } else {
        updateList("videos", array(
            "video_likes" => $youtube_video["statistics"]["likeCount"],
            "video_dislikes" => $youtube_video["statistics"]["dislikeCount"],
            "video_views" => $youtube_video["statistics"]["viewCount"],
        ), "video_id", $video_id);
    }
}


