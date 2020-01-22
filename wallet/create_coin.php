<?php
include_once "login.php";

$coin_name = get("name");
$coin_code = get("code");

if ($coin_name != null && $coin_code != null) {
    $coin_id = insertList("coins", array(
        "coin_name" => $coin_name,
        "coin_code" => $coin_code,
    ));

    $user_id = $user["user_id"];
    for ($i = 0; $i < 64; $i++) {
        $domain_last_active_time = time();
        $insert_domain_keys_sql = "insert into domain_keys (user_id, coin_id, domain_name, domain_next_name) VALUES ";
        $insert_domains_sql = "insert into domains (domain_name, domain_next_hash, domain_last_active_time) VALUES ";
        for ($j = 0; $j < 1024; $j++) {
            $domain_name = mysqli_real_escape_string($GLOBALS["conn"], $coin_name . "\u" . substr("0000".dechex($i * 1024 + $j),-4));
            $domain_next_name = "" . random_id();
            $domain_next_hash = hash("sha256", $domain_next_name);

            $insert_domain_keys_sql .= "($user_id,$coin_id,'$domain_name','$domain_next_name')" . ($j != 1023 ? "," : "");
            $insert_domains_sql .= "('$domain_name','$domain_next_hash',$domain_last_active_time)" . ($j != 1023 ? "," : "");
        }
        query($insert_domain_keys_sql);
        query($insert_domains_sql);
    }

    redirect("wallet", array("token" => $token));
}

?>
<html>
<head>
</head>
<body>
<form method="get">
    <input type="hidden" name="token" value="<?= $token ?>" required><!--
    <input type="hidden" name="description" value="For registration new coin" required>
    <input type="hidden" name="receiver" value="admin" required>
    <input type="hidden" name="coin" value="darkcoin" required>
    <input type="hidden" name="count" value="50" required>-->
    <table>
        <tr>
            <td>
                Coin name:
            </td>
            <td>
                <input type="text" name="name" placeholder="Bitcoin" autocomplete="off" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Coin code:
            </td>
            <td>
                <input type="text" name="code" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off"
                       maxlength="5" placeholder="BTC" required/>
            </td>
        <tr/>
        <tr>
            <td>
            </td>
            <td align="right">
                <button type="submit">Create</button>
            </td>
        <tr/>
    </table>
</form>
</body>
</html>

