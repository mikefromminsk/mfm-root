<?php

include_once "login.php";
header("Content-type: text/html;charset=utf-8");

$order_id = random_id();
send($user_id, "Order created", "Order №$order_id created. You have 15 minutes to pay.", MESSAGE_ORDER_CREATE, $order_id);

?>
<html>
<body>
<form id="submit_form" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
    <input type="hidden" name="receiver" value="<?= $yandex_money_wallet_id ?>">
    <input type="hidden" name="quickpay-form" value="donate">
    <input type="hidden" name="targets" value="For order №<?= $order_id ?>">
    <input type="hidden" name="label" value="<?= $order_id ?>">
    <input type="hidden" name="sum" data-type="number" value="<?= $yandex_money_registration_fee ?>">
</form>
Order №<?= $order_id ?>
Redirecting after 3 seconds...
<script>
    setTimeout(function () {
        document.getElementById("submit_form").submit();
    }, 3000)
</script>
</body>
</html>
