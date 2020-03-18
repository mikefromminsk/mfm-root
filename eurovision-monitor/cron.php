<?php

require $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

$videos = array(
    "V_hgYnwZR8I" => ["country_code" => "bg", "name" => "Victoria - Tears Getting Sober"],
    "O9GAfFHZE-E" => ["country_code" => "ch", "name" => "Gjon's Tears - Répondez-moi"],
    "FxPm-Wz8qpY" => ["country_code" => "lt", "name" => "The Roop - On Fire"],
    "L_dWvTCdDQ4" => ["country_code" => "ru", "name" => "Little Big - Uno"],
    "1HU7ocv3S2o" => ["country_code" => "is", "name" => "Daði & Gagnamagnið - Think About Things"],
    "tPv9ZPXmFWU" => ["country_code" => "it", "name" => "Diodato - Fai rumore"],
    "TmqSU3v_Mtw" => ["country_code" => "ro", "name" => "Roxen - Alcohol You"],
    "CFCn_8oViRw" => ["country_code" => "mt", "name" => "Destiny - All Of My Love"],
    "I0VzBCvO1Wk" => ["country_code" => "az", "name" => "Samira Efendi - Cleopatra"],
    "hAobDQ9GbT4" => ["country_code" => "de", "name" => "Ben Dolic - Violent Thing"],
    "o9atJbnqhJU" => ["country_code" => "no", "name" => "Ulrikke Brandstorp - Attention"],
    "7EpSBDPlZn4" => ["country_code" => "se", "name" => "The Mamas - Move"],
    "sMcxWB90TTY" => ["country_code" => "nl", "name" => "Jeangu Macrooy - Grow"],
    "LjNK4Xywjc4" => ["country_code" => "ge", "name" => "Tornike Kipiani - Take Me As I Am"],
    "XJBrjrQ6vNk" => ["country_code" => "dk", "name" => "Ben & Tan - Yes"],
    "lAqjksxc4iA" => ["country_code" => "be", "name" => "Hooverphonic - Release Me"],
    "dJxCINWp_j0" => ["country_code" => "gr", "name" => "Stefania - Superg!rl"],
    "YjzyZZ-oidc" => ["country_code" => "il", "name" => "Eden Alene - Feker Libi"],
    "5f8jDV6Kcyc" => ["country_code" => "am", "name" => "Athena Manoukian - Chains On You"],
    "s_Y7mMka4SQ" => ["country_code" => "pl", "name" => "Alicja Szemplińska - Empires"],
    "7fqZevYLUMs" => ["country_code" => "rs", "name" => "Hurricane - Hasta la vista"],
    "zNetXPSld50" => ["country_code" => "ua", "name" => "Go_A - Solovey"],
    "6iS-lV909T4" => ["country_code" => "gb", "name" => "Newman - My Last Breath"],
    "HLgE0Ayl5Hc" => ["country_code" => "ie", "name" => "Lesley Roy - Story Of My Life"],
    "94V_m1e0I_Q" => ["country_code" => "cz", "name" => "Benny Cristo - Kemama"],
    "p-E-kIFPrsY" => ["country_code" => "al", "name" => "Arilena Ara - Fall From The Sky"],
    "D02Xlo_LfRU" => ["country_code" => "fr", "name" => "Tom Leeb - The Best in Me"],
    "EgONBKFQpxE" => ["country_code" => "fi", "name" => "A. Kankaanranta - Looking Back"],
    "xPZumQQExQc" => ["country_code" => "mk", "name" => "Македония Vasil - You"],
    "cOuiTJlBC50" => ["country_code" => "at", "name" => "Vincent Bueno - Alive"],
    "RnD1ApDo5_k" => ["country_code" => "md", "name" => "Natalia Gordienko - Prison"],
    "ELr6U2fOrnE" => ["country_code" => "lv", "name" => "Samanta Tīna - Still Breathing"],
    "Jl_qEw_4OK0" => ["country_code" => "cy", "name" => "Sandro Nicolas - Running"],
    "eIZ48w4epng" => ["country_code" => "pt", "name" => "Elisa - Medo de sentir"],
    "c6ZNo_hVA6E" => ["country_code" => "sm", "name" => "Senhit - Freaky!"],
    "3EIQ6U039ms" => ["country_code" => "ee", "name" => "Uku Suviste - What Love Is"],
    "2rOwScdxjJU" => ["country_code" => "hr", "name" => "Damir Kedžo - Divlji vjetre"],
    "F0wfxz5zq04" => ["country_code" => "by", "name" => "VAL - Da vidna"],
    "weLeotNwexg" => ["country_code" => "si", "name" => "Ana Soklič - Voda"],
);

$response = http_json_get("https://www.googleapis.com/youtube/v3/videos?part=statistics&key=AIzaSyDS4NcpuaRv82o3sce2QSejsn6Xa-YeC8w"
    . "&id=" . implode(",", array_keys($videos)));

echo json_encode_readable($response);

foreach ($response["items"] as $youtube_video) {
    $video_id = $youtube_video["id"];
    $video = $videos[$video_id];
    if (scalar("select count(*) from videos where video_id = '" . uencode($video_id) . "'") == 0) {
        insertList("videos", array(
            "video_id" => $video_id,
            "video_country_code" => $video["country_code"],
            "video_name" =>  $video["name"],
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
