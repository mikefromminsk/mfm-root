<?php
include_once "auth.php";

$order_id = get_int(order_id);

$order = selectRowWhere(orders, [order_id => $order_id]);
if ($order == null) error("order not exist");
if ($order[status] != 0) error("cannot cancel");
if ($order["user_id"] != $user_id) error("not yours");

if ($order[is_sell]) {
    $amount = $order[amount] - $order[filled];
    unbBalance($user_id, $order[ticker], $amount);
    incBalance($user_id, $order[ticker], $amount);
} else {
    $amount = ($order[amount] - $order[filled]) * $order[price];
    unbBalance($user_id, USDT, $amount);
    incBalance($user_id, $order[ticker], $amount);
}

$response["result"] = updateWhere(orders, [status => -1], [order_id => $order_id]);

echo json_encode($response);