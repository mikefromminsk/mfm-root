<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darkcoin/api/login.php";

$where = " where user_id = $user_id and message_readied = 0 order by message_id desc limit 20";
echo  json_encode(array(
    "messages" => select("select * from messages $where")
));

update("update messages set message_readied = 1 $where");