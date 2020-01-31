controller("wallet", function ($scope, $routeParams) {
    $scope.toggleSendFragment = true;
    $scope.toggleExchangeFragment = true;
    $scope.toggleCreateCoinFragment = true;

    $scope.show = function (fragmentName){
        $scope.toggleSendFragment = !$scope.toggleSendFragment || fragmentName !== "send";
        $scope.toggleExchangeFragment = !$scope.toggleExchangeFragment || fragmentName !== "exchange";
        $scope.toggleCreateCoinFragment = !$scope.toggleCreateCoinFragment || fragmentName !== "create_coin";
    }

    $scope.walletId = "http://darkwallet.store/user_login";
    $scope.coins = [
        {"coin_code": "USD", "coin_count": 65000},
        {"coin_code": "BTC", "coin_count": 1000},
        {"coin_code": "FTS", "coin_count": 2400},
        {"coin_code": "SEF", "coin_count": 10040},
        {"coin_code": "SIL", "coin_count": 2},
        {"coin_code": "WGS", "coin_count": 1205},
        {"coin_code": "VWD", "coin_count": 62125},
        {"coin_code": "WBWE", "coin_count": 120},
        {"coin_code": "EGW", "coin_count": 1207},
        {"coin_code": "BWQET", "coin_count": 120},
        {"coin_code": "VEQ", "coin_count": 40407},
    ]
    $scope.sendCoin = ""
    $scope.offer_have_coin_code = "BTC"
    $scope.offer_want_coin_code = "USD"
    $scope.have_coin_code = "BTC"
    $scope.want_coin_code = "USD"
    $scope.haveOffers = [
        {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
        {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
    ]
    $scope.saleOffers =[
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
            {"have_coin_code": "BTC", "have_coin_count": 4000, "want_coin_code": "USD", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 40, "offer_type": "Sale"},
    ]

    $scope.buyOffers = [
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
            {"have_coin_code": "USD", "have_coin_count": 4000, "want_coin_code": "BTC", "want_coin_count": "200", "offer_rate": 4.0, "offer_progress": 80, "offer_type": "Buy"},
    ]
})