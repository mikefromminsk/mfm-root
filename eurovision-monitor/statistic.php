<?php
require $_SERVER["DOCUMENT_ROOT"] . "/db-utils/db.php";

$response["videos"] = select("select * from videos order by video_views desc");

echo json_encode_readable($response);