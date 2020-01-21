<?php

include_once "../db.php";

$login = get("login");
$password = get("password");
$token = get("token");

if ($login != null && $password != null)
    redirect("?token=12");

if ($token == null) {
    ?>
    <html>
    <head>
    </head>
    <body>
    <form method="get">
        <table>
            <tr>
                <td>
                    Login:
                </td>
                <td>
                    <input type="text" name="login"/>
                </td>
            <tr/>
            <tr>
                <td>
                    Password:
                </td>
                <td>
                    <input type="password" name="password"/>
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

    <?php
    die();
}
?>

