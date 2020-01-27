<?php
include_once "login.php";
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
                <select>
                    <option value="BTC" disabled>BTC</option>
                    <option value="USD" selected>USD</option>
                </select>
                <input placeholder="0.0001" style="width: 80px">
            </td>
        <tr/>
        <tr>
            <td>
                You receive:
            </td>
            <td>
                <select>
                    <option value="BTC" selected>BTC</option>
                    <option value="USD" disabled>USD</option>
                </select>
                <input placeholder="0.0001" style="width: 80px">
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