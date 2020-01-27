<?php
include_once "../db.php";

?>

<html>
<head>
</head>
<body>
<table align="center">
    <tr align="center">
        <td colspan="2">
            <select>
                <option value="BTC" selected>BTC</option>
                <option value="USD" disabled>USD</option>
            </select>
            /
            <select>
                <option value="BTC" disabled>BTC</option>
                <option value="USD" selected>USD</option>
            </select>
        </td>
    <tr/>
    <tr>
        <td>
            <table border="1" cellspacing="0">
                <tr>
                    <td>
                    </td>
                    <td>
                        has BTC
                    </td>
                    <td>
                        wont USD
                    </td>
                    <td>
                        Rate BTC/USD
                    </td>
                <tr/>
                <tr>
                    <td>
                        <a href="#">sell</a>
                    </td>
                    <td>
                        3,0020
                    </td>
                    <td>
                        ‬‬25 817,2
                    </td>
                    <td>
                        8600
                    </td>
                <tr/>
            </table>
        </td>
        <td>
            <table border="1" cellspacing="0">
                <tr>
                    <td>
                        Rate BTC/BTC
                    </td>
                    <td>
                        wont BTC
                    </td>
                    <td>
                        has USD
                    </td>
                    <td>
                    </td>
                <tr/>
                <tr>
                    <td>
                        8700
                    </td>
                    <td>
                        1,3632
                    </td>
                    <td>
                        11 859,84
                    </td>
                    <td>
                        <a href="#">change</a>
                        <a href="#">delete</a>
                    </td>
                <tr/>
            </table>
        </td>
    <tr/>
    <tr>
        <td><br></td>
    </tr>
    <tr>
        <td align="right">
            <label>You give</label>
            <select>
                <option value="BTC" disabled>BTC</option>
                <option value="USD" selected>USD</option>
            </select>
            <input placeholder="0.0001" style="width: 80px">
        </td>
        <td align="left">
            <label>You receive</label>
            <select>
                <option value="BTC" selected>BTC</option>
                <option value="USD" disabled>USD</option>
            </select>
            <input placeholder="0.0001" style="width: 80px">
            <button>Exchange</button>
        </td>

    </tr>
    <tr>
    </tr>
</table>


</body>
</html>