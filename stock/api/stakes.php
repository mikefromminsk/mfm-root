<?php

include_once "auth.php";

$response[stakes] =
    select("select * from transfers t1 where from_user_id = $user_id and type = 'STAKE' and "
        ."(select amount from transfers t2 where t2.parameter = t1.transfer_id and t2.type = 'UNSTAKE') is null");

echo json_encode($response);