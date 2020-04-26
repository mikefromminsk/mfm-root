<?php
include_once "db.php";

$sql = get_required("sql");

echo json_encode(scalar($sql));