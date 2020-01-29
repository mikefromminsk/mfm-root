<?php
include_once "login.php";

$have_coin_code = get("have_coin_code");
$have_coin_count = get_int("have_coin_count");
$want_coin_code = get("want_coin_code");
$want_coin_count = get_int("want_coin_count");

if ($have_coin_code != null && $have_coin_count != null && $want_coin_code != null && $want_coin_count != null) {
    $request = array(
        "stock_token" => $user["user_stock_token"],
        "have_coin_code" => $have_coin_code,
        "have_coin_count" => $have_coin_count,
        "want_coin_code" => $want_coin_code,
        "want_coin_count" => $want_coin_count,
        "back_url" => $host_url . "receive_domain_keys.php",
    );

    $max_request_coin_count = 1024;
    $request_count = ceil($have_coin_count / $max_request_coin_count);
    for ($i = 0; $i < $request_count; $i++) {
        $coin_count = $i == $request_count - 1 ? bcmod( $have_coin_count, $max_request_coin_count) : $i * $max_request_coin_count;
        $domains_where = " user_id = $user_id and coin_code = '$have_coin_code' limit $coin_count";
        $request["have_domain_keys"] = select("select domain_name, domain_next_name from domain_keys where $domains_where");
        query("delete from domain_keys where $domains_where");

        $response = http_json_post($exchange_server_dir . "create_offer", $request);
        die(json_encode($response));
    }

    //redirect("stock", array("stock_token", $user["user_stock_token"]));
}
?>
<html>
<head>
</head>
<body>
<form method="get">
    <input type="hidden" name="token" value="<?= $token ?>" required>
    <table>
        <tr>
            <td>
                You give:
            </td>
            <td>
                <select name="have_coin_code">
                    <?php
                    $user_coins = select("select t2.*, COUNT(*) as coin_count from domain_keys t1 "
                    . "left join coins t2 on t1.coin_code = t2.coin_code "
                    . "where t1.user_id = $user_id group by t1.coin_code order by coin_count desc");
                    $first_my_coin = $user_coins[0]["coin_code"];
                    foreach ($user_coins as $coin) { ?>
                        <option value="<?= $coin['coin_code'] ?>" <?= $coin['coin_code'] == $first_my_coin ? 'selected' : '' ?>><?= $coin['coin_name'] ?></option>
                    <?php } ?>
                </select>
                <input name="have_coin_count" value="5" style="width: 80px">
            </td>
        <tr/>
        <tr>
            <td>
                You receive:
            </td>
            <td>
                <select name="want_coin_code">
                    <?php foreach (select("select * from coins") as $coin) { ?>
                        <option value="<?= $coin['coin_code'] ?>" <?= $coin['coin_code'] != $first_my_coin ? 'selected' : '' ?>><?= $coin['coin_name'] ?></option>
                    <?php } ?>
                </select>
                <input name="want_coin_count" value="20" style="width: 80px">
            </td>
        <tr/>
        <tr>
            <td>
            </td>
            <td align="right">
                <button type="submit">Exchange</button>
            </td>
        <tr/>
    </table>
</form>


</body>
</html>