<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/telegram/api/utils.php";

$bot = get_required(bot);
$message = get_required(message);

$chat_id = $message[chat][id];
$username = $message[chat][username];


if (!dataExist([users, $username, $bot]))
    dataSet([users, $username, $bot], $chat_id);


spendGasOf(get_required(gas_address), get_required(gas_password));
commit();

telegramSendToUsername($bot, $username, "Hello, $username! Tap Play button.");


/*"message":{
    "date":1441645532,
  "chat":{
        "last_name":"Test Lastname",
     "id":1111111,
     "type": "private",
     "first_name":"Test Firstname",
     "username":"Testusername"
  },
  "message_id":1365,
  "from":{
        "last_name":"Test Lastname",
     "id":1111111,
     "first_name":"Test Firstname",
     "username":"Testusername"
  },
  "forward_from": {
        "last_name":"Forward Lastname",
     "id": 222222,
     "first_name":"Forward Firstname"
  },
  "forward_date":1441645550,
  "text":"/start"
}*/

