<?php

include_once "../db.php";

$login = get("login");
$password = get("password");
$token = get("token");
$message = "";

if ($login != null && $password != null) {
    $user = selectMap("select * from users where user_login = '$login'");
    $password_hash = hash("sha256", $password);
    if ($user != null && $user["user_password_hash"] != $password_hash) {
        $message = "Password is not correct";
    } else {
        $token = random_id();
        if ($user != null) {
            if ($user["user_password_hash"] == $password_hash) {
                updateList("users", array(
                    "user_session_token" => $token
                ), "user_id", $user["user_id"]);
            } else {
            }
        } else {
            insertList("users", array(
                "user_login" => $login,
                "user_password_hash" => $password_hash,
                "user_session_token" => $token,
            ));
        }
        unset($_GET["login"]);
        unset($_GET["password"]);
        $_GET["token"] = $token;
        redirect("wallet.php", $_GET);
    }
}

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
                    <input type="text" name="login" value="<?= $login ?>" required/>
                </td>
            <tr/>
            <tr>
                <td>
                    Password:
                </td>
                <td>
                    <input type="password" name="password" required/>
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
                <td align="right">
                    <button type="submit">Sign up</button>
                </td>
                <td align="right">
                    <button type="submit">Sign in</button>
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

