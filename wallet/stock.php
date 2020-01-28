<?php
include_once "login.php";

$have_coin_code = get_required("have_coin_code");
$want_coin_code = get_required("want_coin_code");

$coins = select("select * from coins");

$buy_offers = select("select * from offers where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate limit 20");
$sell_offers = select("select * from offers where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate desc limit 20");

?>
<html>
<head>
</head>
<body>
<form method="get">
    <table align="center">
        <tr align="center">
            <td colspan="2">
                <select name="coin_first">
                    <?php foreach ($coins as $coin) { ?>
                        <option value="<?= $coin["coin_code"] ?>" <?= $have_coin_code == $coin["coin_code"] ? "selected" : "" ?>>
                            <?= $coin["coin_code"] ?>
                        </option>
                    <?php } ?>
                </select>
                :
                <select name="coin_second">
                    <?php foreach ($coins as $coin) { ?>
                        <option value="<?= $coin["coin_code"] ?>" <?= $want_coin_code == $coin["coin_code"] ? "selected" : "" ?>>
                            <?= $coin["coin_code"] ?>
                        </option>
                    <?php } ?>
                </select>
            </td>
        <tr/>
        <tr>
            <td>
                <table border="1" cellspacing="0">
                    <tr>
                        <td>
                        </td>
                        <td>
                            Has <?= $have_coin_code ?>
                        </td>
                        <td>
                            Want <?= $want_coin_code ?>
                        </td>
                        <td>
                            Rate <?= $have_coin_code . "/" . $want_coin_code ?>
                        </td>
                    <tr/>
                    <?php foreach ($buy_offers as $buy_offer) { ?>
                        <tr>
                            <td>
                                <?php if ($buy_offer["user_id"] == $user["user_id"]) { ?>
                                    <a href="#">change</a>
                                    <a href="#">delete</a>
                                <?php } ?>
                            </td>
                            <td>
                                <?= $buy_offer["have_coin_count"] ?>
                            </td>
                            <td>
                                <?= $buy_offer["want_coin_count"] ?>
                            </td>
                            <td>
                                <?= $buy_offer["offer_rate"] ?>
                            </td>
                        <tr/>
                    <?php } ?>
                </table>
            </td>
            <td>
                <table border="1" cellspacing="0">
                    <tr>
                        <td>
                            Rate <?= $have_coin_code . "/" . $want_coin_code ?>
                        </td>
                        <td>
                            Want <?= $want_coin_code ?>
                        </td>
                        <td>
                            Has <?= $have_coin_code ?>
                        </td>
                        <td>
                        </td>
                    <tr/>
                    <?php foreach ($sell_offers as $sell_offer) { ?>
                        <tr>
                            <td>
                                <?= $sell_offer["offer_rate"] ?>
                            </td>
                            <td>
                                <?= $sell_offer["have_coin_count"] ?>
                            </td>
                            <td>
                                <?= $sell_offer["want_coin_count"] ?>
                            </td>
                            <td>
                                <?php if ($sell_offer["user_id"] == $user["user_id"]) { ?>
                                    <a href="#">change</a>
                                    <a href="#">delete</a>
                                <?php } ?>
                            </td>
                        <tr/>
                    <?php } ?>
                </table>
            </td>
        <tr/>
    </table>
</form>

</body>
</html>