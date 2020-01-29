<?php

include_once "../db.php";
$node_url = uencode($node_url);

$user_login = get("user_login");
$user_password = get("user_password");
$token = get_int("token");
$stock_token = get("stock_token");
$message = "";
$user = null;
$user_id = null;

if ($stock_token != null) {
    $user = selectMap("select * from users where user_session_token = $stock_token");
    if ($user == null)
        insertList("users", array(
            "user_login" => "user" . rand(1, 1000000),
            "user_password_hash" => hash("sha256", "pass" . rand(1, 1000000)),
            "user_session_token" => $stock_token,
            "user_stock_token" => random_id(),
        ));
    $token = $stock_token;
}

if ($user_login != null && $user_password != null) {
    $user = selectMap("select * from users where user_login = '$user_login'");
    $password_hash = hash("sha256", $user_password);
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
            $stock_token = random_id();
            insertList("users", array(
                "user_login" => $user_login,
                "user_password_hash" => $password_hash,
                "user_session_token" => $token,
                "user_stock_token" => $stock_token,
            ));
        }
        unset($_GET["login"]);
        unset($_GET["password"]);
        $_GET["token"] = $token;
        redirect("wallet", $_GET);
    }
}

if ($user == null && $token != null)
    $user = selectMap("select * from users where user_session_token = $token");


$user_id = $user["user_id"];

if ($user_id == null) {
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
                    <input name="user_login" type="text" value="<?= $user_login ?>" required/>
                </td>
            <tr/>
            <tr>
                <td>
                    Password:
                </td>
                <td>
                    <input name="user_password" type="password" required/>
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

