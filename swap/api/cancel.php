<?php



function cancel($user_id, $order_id)
{
    $order = selectRowWhere(orders, [order_id => $order_id]);
    if ($order == null) error("order not exist");
    if ($order[status] != 0) error("cannot cancel");
    if ($order["user_id"] != $user_id) error("not yours");

    if ($order[is_sell]) {
        $amount = round($order[amount] - $order[filled], 2);
        unbBalance($user_id, $order[ticker], $amount);
        incBalance($user_id, $order[ticker], $amount);
    } else {
        $amount = round(($order[amount] - $order[filled]) * $order[price], 4);
        unbBalance($user_id, USDT, $amount);
        incBalance($user_id, $order[ticker], $amount);
    }
    return updateWhere(orders, [status => -1], [order_id => $order_id]);
}