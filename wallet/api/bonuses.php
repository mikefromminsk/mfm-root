<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/utils.php";

$bonuses = get_required(bonuses);

$bonuses = explode(",", $bonuses);

$bonus_domains = [];
foreach ($bonuses as $bonus) {
    $bonus = explode(":", $bonus);
    $domain = $bonus[0];
    $bonus_key = $bonus[1];
    $bonus_hash = md5($bonus_key);
    $amount = dataGet([$domain, bonus, $bonus_hash, amount]);
    if ($amount != null && $amount != 0) {
        $bonus_domains[$domain] = true;
        $GLOBALS[response][bonuses][] = [
            domain => $domain,
            bonus_hash => $bonus_hash,
            bonus_key => $bonus_key,
            amount => $amount,
        ];
    }
}

$GLOBALS[domains] = implode(",", array_keys($bonus_domains));

if ($GLOBALS[domains]){
    include_once $_SERVER["DOCUMENT_ROOT"] . "/wallet/api/list.php";
} else {
    commit([]);
}
