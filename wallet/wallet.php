<?php

include_once "login.php";

?>
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
            <?php
            $coins = select("select *, count(*) as 'coin_count' from domain_keys t1"
                . " left join coins t2 on t1.coin_id = t2.coin_id"
                . " where t1.user_id = " . $user["user_id"]
                . " group by t1.coin_id ");
            foreach ($coins as $coin) { ?>
                <br>
                <a href="send?token=<?= $token ?>&coin=<?= $coin["coin_name"] ?>">
                    <div style="background-color: lightgray;">
                        <big><b><?=$coin["coin_count"]?></b> <?=$coin["coin_code"]?></big><br>
                        <small>1 DarkCoin = 0.001 USD</small>
                    </div>
                </a>
            <?php } ?>
            <br>
            <a href="create_coin?token=<?= $token ?>">
                <button>Create coin</button>
            </a>
        </td>
    </tr>
</table>
</body>
</html>
