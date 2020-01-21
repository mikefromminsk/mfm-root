<?php include_once "login.php"; ?>
<html>
<head>
</head>
<body>
<table>
    <tr>
        <th>Wallet user_login</th>
    </tr>
    <tr>
        <td>
            <input id="wallet_addr" value="123" disabled/>
            <button onclick="this.innerText = 'coped';">copy</button>
            <br>
            <br>
            <a href="exchange.php?token=<?= $token ?>&coin=bitcoin">
                <div style="background-color: lightgray;">
                    <big><b>10012</b> DRK</big><br>
                    <small>1 DarkCoin = 0.001 USD</small>
                </div>
            </a>
            <br>
            <a href="create_coin.php?token=<?= $token ?>">
                <button>Create coin</button>
            </a>
        </td>
    </tr>
</table>
</body>
</html>
