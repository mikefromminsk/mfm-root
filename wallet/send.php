<?php

include_once "login.php";

$coin_name = get_required("coin");
$coin_count = get_int("count");
$receiver_user_login = get("receiver");

$message = "";

if ($coin_name != null && $coin_count != null && $receiver_user_login != null) {
    $receiver = selectMap("select * from users where user_login = '$receiver_user_login'");
    if ($receiver != null) {
        $coin = selectMap("select * from coins where coin_name = '$coin_name'");
        if ($coin != null) {
            $coins = selectList("select * from domain_keys where user_id = " . $user["user_id"] . " and coin_id = " . $coin["coin_id"] . " limit $coin_count");
            if (sizeof($coins) == $coin_count) {
                update("update domain_keys set user_id = " . $receiver["user_id"] . " where user_id = " . $user["user_id"] . " limit $coin_count");
            } else
                $message = "not enough coins";
        } else
            $message = "coin doesnt exist in wallet";
    } else
        $message = "receiver doesnt exist";

    if ($message == "")
        redirect("wallet", array("token" => $token));
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
                Receiver:
            </td>
            <td>
                <input type="text" name="receiver" value="<?= $receiver_user_login ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Coin:
            </td>
            <td>
                <input type="text" name="coin" value="<?= $coin_name ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Count:
            </td>
            <td>
                <input type="number" name="count"  value="<?= $coin_count ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
            </td>
            <td align="right" style="color: red">
                <?= $message ?>
            </td>
        <tr/>
        <tr>
            <td>
            </td>
            <td align="right">
                <input type="submit"/>
            </td>
        <tr/>
    </table>
</form>
</body>
</html>
