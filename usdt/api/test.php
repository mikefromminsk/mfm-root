<?php

include_once $_SERVER["DOCUMENT_ROOT"] . "/usdt/api/utils.php";

//https://api.bscscan.com/api?module=account&action=tokentx&address=0x1e0426Ba2E77eDdf7FfB19C57B992c4dcC6455F4&startblock=0&endblock=99999999&page=1&offset=10&sort=asc&apikey=599AYTGK469V5FVTNI9RU8CQ46K794HMC8&contractaddress=0x55d398326f99059ff775485246999027b3197955

/*         "contractAddress":"0x0e09fabb73bd3ade0a17ecc321fd13a19e81ce82",
         "tokenName":"PancakeSwap Token",
         "symbol":"Cake",
         "divisor":"18",
         "tokenType":"ERC20",
         "totalSupply":"431889535.843059000000000000",
         "blueCheckmark":"true",
         "description":"PancakeSwap is a yield farming project whereby users can get FLIP (LP token) for staking and get CAKE token as reward. CAKE holders can swap CAKE for SYRUP for additional incentivized staking.",
         "website":"https://pancakeswap.finance/",
         "email":"PancakeSwap@gmail.com",
         "blog":"https://medium.com/@pancakeswap",
         "reddit":"",
         "slack":"",
         "facebook":"",
         "twitter":"https://twitter.com/pancakeswap",
         "bitcointalk":"",
         "github":"https://github.com/pancakeswap",
         "telegram":"https://t.me/PancakeSwap",
         "wechat":"",
         "linkedin":"",
         "discord":"",
         "whitepaper":"",
         "tokenPriceUSD":"23.9300000000"*/
echo json_encode(usdtTrc20Transactions("TSXvWWCsysLQoPujPCEbYQySXP66ZvN57b"));