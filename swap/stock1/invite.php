<?php

include_once "api/utils.php";

$ticker = get_required("t");
$inviter = get_required("i");

$token = random_key(users, token);

$user_id = createUser($token, get_string(email));

$drop = selectRowWhere(drops, [ticker => $ticker]);

$referrer_code = random_key(users, referrer_code);

updateWhere(users, [inviter => $inviter, drop_id => $drop[drop_id], referrer_code => $referrer_code], [user_id => $user_id]);



function redirect($url, $params = array(), $params_in_url = true)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if ($params_in_url == true) {
            $url_params = "";
            foreach ($params as $key => $value)
                $url_params .= "&" . urlencode($key) . "=" . urlencode($value);
            if (strpos($url, "?") === false && $url_params != "")
                $url_params[0] = "?";
            $url .= $url_params;
        }
        $redirect_script = '<html><body><form id="redirect" action="' . $url . '" method="post">';
        if ($params_in_url == false)
            foreach ($params as $key => $value)
                $redirect_script .= '<input type="hidden" name="' . htmlentities($key) . '" value="' . htmlentities(json_encode($value)) . '">';
        $redirect_script .= '</form><script>document.getElementById("redirect").submit();</script></body></html>';
        header("Content-type: text/html;charset=utf-8");
        header("Location: $url");
        die($redirect_script);
    }
}

redirect("https://play.google.com/store/apps/details?id=com.zhiliaoapp.musically&hl=ru&gl=US&referrer=$referrer_code");
