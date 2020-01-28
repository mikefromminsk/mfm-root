<?php

include_once "login.php";

$coin_code = get("coin_code");
$coin_count = get_int("coin_count");
$receiver_user_login = get("receiver_user_login");

$message = "";

if ($coin_code != null && $coin_count != null && $receiver_user_login != null) {
    $receiver = selectMap("select * from users where user_login = '$receiver_user_login'");
    if ($receiver != null) {
        if ($receiver["user_id"] != $user["user_id"]) {

            $domain_names = selectList("select domain_name from domain_keys where user_id = $user_id and coin_code = '$coin_code' limit $coin_count");
            if (sizeof($domain_names) == $coin_count) {

                foreach ($domain_names as $index => $domain_name)
                    $domain_names[$index] = uencode($domain_name);
                update("update domain_keys set user_id = " . $receiver["user_id"]
                    . " where user_id = $user_id and coin_code = '$coin_code' and domain_name in ('" . implode("','", $domain_names) . "')");

                $request_data = array("domains" => selectList("select * from domains where domain_name in ('" . implode("','", $domain_names) . "')"));
                $node_locations = selectList("select distinct node_location from domains where node_location <> '$node_url' limit 5") ?: $start_node_locations;
                foreach ($node_locations as $node_location)
                    http_json_post($node_location, $request_data);
            } else
                $message = "not enough coins";
        } else
            $message = "you cannot send coins to yourself";
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
                <input type="text" name="receiver_user_login" value="<?= $receiver_user_login ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Coin:
            </td>
            <td>
                <input type="text" name="coin_code" value="<?= $coin_code ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Count:
            </td>
            <td>
                <input type="number" name="coin_count" value="<?= $coin_count ?>" required/>
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
