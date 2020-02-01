controller("DarkWallet", function ($scope, $window, $http,
                                   api_url, client_url, exchange_api_url) {
    $scope.activeWindow = 0;
    $scope.windowWidth = $window.innerWidth;
    angular.element($window).bind('resize', function () {
        $scope.windowWidth = $window.innerWidth;
        $scope.$apply();
    });

    $scope.toggleSendFragment = true;
    $scope.toggleExchangeFragment = true;
    $scope.toggleCreateCoinFragment = true;

    $scope.show = function (fragmentName) {
        $scope.toggleSendFragment = !$scope.toggleSendFragment || fragmentName !== "send";
        $scope.toggleExchangeFragment = !$scope.toggleExchangeFragment || fragmentName !== "exchange";
        $scope.toggleCreateCoinFragment = !$scope.toggleCreateCoinFragment || fragmentName !== "create_coin";
    }


    $scope.copyButtonLabel = "Copy Wallet ID"
    $scope.copyToClipboard = function (text) {
        var copyText = document.getElementById("walletId");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        copyText.setSelectionRange(0, 0);
        copyText.blur();
        $scope.copyButtonLabel = 'coped';
    }

    var login = store.get("user_login");
    var token = store.get("user_session_token");
    var stock_token = store.get("user_session_token");

    $scope.walletId = client_url + "#!/send/" + login;

    if (login == null || token == null || stock_token == null)
        $scope.open('login');
    else {
        $http.post(api_url + "wallet.php", {
            token: token,
        }).then(function (response) {
            if (response.data.message == null) {
                $scope.coins = response.data.coins;
            }
        })
        $http.post(exchange_api_url + "stock.php", {
            token: token,
        }).then(function (response) {
            if (response.data.message == null) {
                $scope.saleOffers = response.data.sale_offers;
                $scope.buyOffers = response.data.buy_offers;
                $scope.haveOffers = response.data.have_offers;
                $scope.rates = response.data.rates;
            }
        })

    }

    $scope.sendCoin = ""
    $scope.offer_have_coin_code = "BTC"
    $scope.offer_want_coin_code = "USD"
    $scope.have_coin_code = "BTC"
    $scope.want_coin_code = "USD"
    $scope.haveOffers = [
        {
            "have_coin_code": "USD",
            "have_coin_count": 4000,
            "want_coin_code": "BTC",
            "want_coin_count": "200",
            "offer_rate": 4.0,
            "offer_progress": 80,
            "offer_type": "Buy"
        },
    ]
    $scope.saleOffers = [
        {
            "have_coin_code": "BTC",
            "have_coin_count": 4000,
            "want_coin_code": "USD",
            "want_coin_count": "200",
            "offer_rate": 4.0,
            "offer_progress": 40,
            "offer_type": "Sale"
        },
    ]

    $scope.buyOffers = [
        {
            "have_coin_code": "USD",
            "have_coin_count": 4000,
            "want_coin_code": "BTC",
            "want_coin_count": "200",
            "offer_rate": 4.0,
            "offer_progress": 80,
            "offer_type": "Buy"
        },
    ]

})