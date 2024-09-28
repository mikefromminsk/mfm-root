<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/db/db.php";

$update_id = get_required(update_id);
$message = get_required(message);

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

$response[response] = http_post("https://api.telegram.org/bot$telegram_bot_api/sendMessage", [
    chat_id => $message[chat][id],
    text => "wewf",
]);

$response[success] = true;

file_put_contents("echo.json", json_encode($response));
