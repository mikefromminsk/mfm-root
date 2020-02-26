<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/darklogin/login.php";
header("Content-type: text/html;charset=utf-8");

$coin_name = get_required("coin_name");
$coin_code = get_required("coin_code");
$coin_code = strtoupper($coin_code);

$admin_login = scalar("select user_login from users where user_id = 0");

$admin_usd_count = scalar("select count(*) from domain_keys where coin_code = 'USD'");

if ($admin_usd_count < $stock_fee_in_usd)
    error("Admin doesnt have enough usd");

$order_id = random_id();
send($user_id, "Order created", "Order №$order_id created. You have 15 minutes to pay.", MESSAGE_ORDER_CREATE, $order_id);

?>
<html>
<body>
<form id="submit_form" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
    <input type="hidden" name="receiver" value="<?= $yandex_money_wallet_id ?>">
    <input type="hidden" name="quickpay-form" value="donate">
    <input type="hidden" name="targets" value="For order №<?= $order_id ?>">
    <input type="hidden" name="paymentType" value="AC">
    <input type="hidden" name="label" value="<?= "coin_code=$coin_code&coin_name=$coin_name&order_id=$order_id" ?>">
    <input type="hidden" name="sum" data-type="number" value="<?= $stock_fee_in_rub ?>">
    <button type="submit">Redirect</button>
</form>
Order №<?= $order_id ?>
Redirecting ...
<script>
    //document.getElementById("submit_form").submit();
</script>
</body>
</html>
