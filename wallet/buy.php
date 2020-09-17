<!DOCTYPE html>
<html>
<head>
    <?php include_once "head.php" ?>
    <title>Wallet</title>
    <script src="controllers/buy.js?<?=time()?>"></script>
</head>
<body style="background-color: #eeeeee" class="col fill" ng-controller="buy">
<div class="row align-center-center flex">
    <div class="col inputs" id="login">
        <div class="row align-center-center">
            <h2>Please Login</h2>
        </div>
        <form ng-submit="buy()">
            <div class="col">
                <input class="flex" type="number" ng-model="amount" step="0.01" required placeholder="Сумма *" ng-disabled="login_requesting">
                <h5>* Включая комиссию яндекс.деньги 2%</h5>
                <input class="flex" type="number" ng-model="amount_due" step="0.01" required placeholder="К получению" ng-disabled="login_requesting">
                <input class="flex-40" type="submit" value="Buy" ng-disabled="buy_requesting">
            </div>
            <h4 ng-if="buy_error" style="color: red">Request error</h4>
        </form>
        <form id="go_yandex" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml" style="display: none">
            <input type="hidden" name="receiver" value="410016298318202">
            <input type="hidden" name="formcomment" value="Проект «Железный человек»: реактор холодного ядерного синтеза">
            <input type="hidden" name="short-dest" value="Проект «Железный человек»: реактор холодного ядерного синтеза">
            <input type="hidden" name="label" id="label">
            <input type="hidden" name="quickpay-form" value="donate">
            <input type="hidden" name="targets" id="targets">
            <input type="number" name="sum" id="amount">
            <input type="hidden" name="comment" value="Хотелось бы дистанционного управления.">
            <input type="hidden" name="paymentType" value="AC">
        </form>
    </div>
</div>
</body>
</html>