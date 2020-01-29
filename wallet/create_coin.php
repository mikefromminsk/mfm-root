<?php

include_once "login.php";

$coin_name = get("coin_name");
$coin_code = get("coin_code");
$message = "";

function unichr($u)
{
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
}

if ($coin_name != null && $coin_code != null) {
    $success = insertList("coins", array(
        "coin_name" => $coin_name,
        "coin_code" => $coin_code,
    ));
    if ($success) {
        $user_id = $user["user_id"];
        for ($i = 0; $i < 64; $i++) {
            $domain_last_online_time = time();
            $insert_domain_keys_sql = "insert into domain_keys (user_id, coin_code, domain_name, domain_next_name) VALUES ";
            $insert_domains_sql = "insert into domains (domain_name, domain_next_hash, domain_last_online_time, node_location) VALUES ";
            for ($j = 0; $j < 1024; $j++) {
                $domain_name = uencode($coin_name . unichr($i * 1024 + $j));
                $domain_next_name = "" . random_id();
                $domain_next_hash = hash("sha256", $domain_next_name);
                $insert_domain_keys_sql .= "($user_id,'$coin_code','$domain_name','$domain_next_name')" . ($j != 1023 ? "," : "");
                $insert_domains_sql .= "('$domain_name','$domain_next_hash',$domain_last_online_time, '$node_url')" . ($j != 1023 ? "," : "");
            }
            query($insert_domain_keys_sql);
            query($insert_domains_sql);
        }

        $max_block_domains = 65536;
        $request_count = 8;
        $domains_per_request = $max_block_domains / $request_count;
        $node_locations = selectList("select distinct node_location from domains where node_location <> '$node_url' limit 5") ?: $start_node_locations;
        foreach ($node_locations as $node_location) {
            for ($i = 0; $i < $request_count; $i++) {
                $domain_list = select("select * from domains where domain_name like '$coin_name%' "
                    . " limit " . ($i * $domains_per_request - ($i == 0 ? 0 : 1) . ", $domains_per_request"));
                http_json_post($node_location, array("domains" => $domain_list));
            }
        }
        redirect("wallet", array("token" => $token));
    } else
        $message = "coin name exist";
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
                <input name="coin_name" type="text" placeholder="Bitcoin" autocomplete="off" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Coin code:
            </td>
            <td>
                <input name="coin_code" type="text" onkeyup="this.value = this.value.toUpperCase();" autocomplete="off"
                       maxlength="5" placeholder="BTC" required/>
            </td>
        <tr/>
        <tr>
            <td>
            </td>
            <td align="right">
                <span style="color: red"><?= $message ?></span>
                <button type="submit">Create</button>
            </td>
        <tr/>
    </table>
</form>
</body>
</html>

