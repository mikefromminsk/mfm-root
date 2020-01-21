<?php

include_once "login.php";

$coin = get_required("coin");
$count = get("count");
$receiver = get("receiver");
$description = get("description");
$redirect = get("redirect");
$message = "";
if ($coin != null && $count != null && $receiver != null) {
    if ($count > 100)
        $message = "code doesnt exist";
    else
        redirect($redirect ?: "wallet.php?token=" . $token);
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
                Description:
            </td>
            <td>
                <input type="text" name="description" value="<?=$description?>" />
            </td>
        <tr/>
        <tr>
            <td>
                Receiver:
            </td>
            <td>
                <input type="text" name="receiver" value="<?=$receiver?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Coin:
            </td>
            <td>
                <input type="text" name="coin" value="<?= $coin ?>" required/>
            </td>
        <tr/>
        <tr>
            <td>
                Count:
            </td>
            <td>
                <input type="number" name="count" required/>
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
