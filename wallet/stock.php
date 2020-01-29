<?php
include_once "domain_utils.php";

$have_coin_code = get("have_coin_code");
$want_coin_code = get("want_coin_code");
$delete_offer_id = get_int("delete_offer_id");

if ($delete_offer_id != null) {
    $delete_offer = selectMap("select * from offers where offer_id = $delete_offer_id");
    if ($delete_offer != null && $delete_offer["user_id"] == $user_id) {
        $request = array(
            "back_user_login" => $delete_offer["back_user_login"],
            "coin_code" => $delete_offer["have_coin_code"],
            "domain_keys" => getDomainKeys($delete_offer["user_id"], $delete_offer["have_coin_code"], $delete_offer["have_coin_count"]),
        );
        $response = http_json_post($delete_offer["back_host_url"], $request);
        query("delete from offers where offer_id = $delete_offer_id");
        redirect($host_url . "wallet?user_login=" . $delete_offer["back_user_login"]);
    }
}

$coins = select("select * from coins");

$buy_offers = select("select * from offers where have_coin_code = '$have_coin_code' and want_coin_code = '$want_coin_code' order by offer_rate limit 20");
$sell_offers = select("select * from offers where have_coin_code = '$want_coin_code' and want_coin_code = '$have_coin_code' order by offer_rate desc limit 20");
?>
<html>
<head>
    <style>
        tr:empty {
            visibility: hidden;
        }
    </style>
</head>
<body>
<form method="get">
    <table align="center">
        <tr align="center" style="display: <?= $have_coin_code != null && $want_coin_code != null ? "block": "none"?>">
            <td colspan="2">
                <?= $have_coin_code ?>:<?= $want_coin_code ?>
            </td>
        <tr/>
        <tr style="display: <?= $have_coin_code != null && $want_coin_code != null ? "inherit": "none"?>">
            <td style="vertical-align: top">
                <table border="1" cellspacing="0">
                    <tr>
                        <td>
                            Owner
                        </td>
                        <td>
                            Have <?= $have_coin_code ?>
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
                                <?= $buy_offer["back_user_login"] ?>
                            </td>
                            <td>
                                <?= $buy_offer["have_coin_count"] ?>
                            </td>
                            <td>
                                <?= $buy_offer["want_coin_count"] ?>
                            </td>
                            <td>
                                <?= round($buy_offer["offer_rate_inverse"], 4) ?>
                            </td>
                        <tr/>
                    <?php } ?>
                </table>
            </td>
            <td style="vertical-align: top">
                <table border="1" cellspacing="0">
                    <tr>
                        <td>
                            Rate <?= $have_coin_code . "/" . $want_coin_code ?>
                        </td>
                        <td>
                            Want <?= $have_coin_code ?>
                        </td>
                        <td>
                            Have <?= $want_coin_code ?>
                        </td>
                        <td>
                            Owner
                        </td>
                    <tr/>
                    <?php foreach ($sell_offers as $sell_offer) { ?>
                        <tr>
                            <td>
                                <?= round($sell_offer["offer_rate"], 4) ?>
                            </td>
                            <td>
                                <?= $sell_offer["have_coin_count"] ?>
                            </td>
                            <td>
                                <?= $sell_offer["want_coin_count"] ?>
                            </td>
                            <td>
                                <?= $buy_offer["back_user_login"] ?>
                            </td>
                        <tr/>
                    <?php } ?>
                </table>
            </td>
        <tr/>
        <tr>
            <td>
                Pairs:
                <?php
                $pairs = select("select * from offers group by have_coin_code");
                foreach ($pairs as $pair) { ?>
                    <a href="?&have_coin_code=<?= $pair["have_coin_code"] ?>&want_coin_code=<?= $pair["want_coin_code"] ?>">
                        <?= $pair["have_coin_code"] ?>:<?= $pair["want_coin_code"] ?>
                    </a>
                    <span> </span>
                <?php } ?>
            </td>
        </tr>
    </table>
</form>

</body>
</html>