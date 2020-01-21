<?php
include_once "login.php";

$name = get("name");
$code = get("code");

?>
<html>
<head>
</head>
<body>
<form method="get" action="exchange.php">
    <input type="hidden" name="token" value="<?= $token ?>" required>
    <input type="hidden" name="description" value="For registration new coin" required>
    <input type="hidden" name="receiver" value="admin" required>
    <input type="hidden" name="coin" value="darkcoin" required>
    <input type="hidden" name="count" value="50" required>
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

