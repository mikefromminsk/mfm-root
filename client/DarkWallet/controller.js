controller("DarkWallet", function ($scope, $window, $http,
                                   api_url, client_url, exchange_api_url) {
    $scope.activeWindow = 0;
    $scope.windowWidth = $window.innerWidth;
    angular.element($window).bind('resize', function () {
        $scope.windowWidth = $window.innerWidth;
        $scope.$apply();
    });

    $scope.toggleSendFragment = true;
    $scope.toggleExchangeFragment = false;
    $scope.toggleCreateCoinFragment = true;

    $scope.show = function (fragmentName) {
        $scope.toggleSendFragment = !$scope.toggleSendFragment || fragmentName !== "send";
        $scope.toggleExchangeFragment = !$scope.toggleExchangeFragment || fragmentName !== "exchange";
        $scope.toggleCreateCoinFragment = !$scope.toggleCreateCoinFragment || fragmentName !== "create_coin";
    }

    $scope.logout = function(){
        store.clear();
        $scope.open('login');
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

    $scope.walletId = login;

    function updateData() {
        $http.post(api_url + "wallet.php", {
            token: token,
        }).then(function (response) {
            if (response.data.message == null) {
                $scope.coins = response.data.coins;
                $scope.have_coins = response.data.have_coins;
                $scope.have_coin_code = $scope.have_coins[0]["coin_code"]
                $scope.want_coin_code = $scope.coins[0] === $scope.have_coin_code ? $scope.coins[1] : $scope.coins[0]
                $scope.offer_have_coin_code = $scope.have_coin_code
                $scope.offer_want_coin_code = $scope.want_coin_code

                $http.post(exchange_api_url + "stock.php", {
                    token: token,
                    have_coin_code: $scope.have_coin_code,
                    want_coin_code: $scope.want_coin_code,
                }).then(function (response) {
                    if (response.data.message == null) {
                        $scope.saleOffers = response.data.sale_offers;
                        $scope.buyOffers = response.data.buy_offers;
                        $scope.haveOffers = response.data.have_offers;
                        $scope.rates = response.data.rates;
                    }
                })
            }
        })
    }

    if (login == null || token == null || stock_token == null)
        $scope.open('login');
    else {
        updateData();
    }

    $scope.create_coin_message = null
    $scope.create_coin_request_in_progress = false
    $scope.newCoinName = ""
    $scope.newCoinCode = ""
    $scope.createCoin = function () {
        $scope.create_coin_message = null
        $scope.create_coin_request_in_progress = true;
        $http.post(api_url + "create_coin.php", {
            token: token,
            coin_name: $scope.newCoinName,
            coin_code: $scope.newCoinCode,
        }).then(function (response) {
            $scope.create_coin_request_in_progress = false;
            if (response.data.message == null) {
                $scope.newCoinName = "";
                $scope.newCoinCode = "";
                $scope.toggleCreateCoinFragment = true;
                updateData();
            } else
                $scope.create_coin_message = response.data.message
        })
    };

    $scope.exchange_in_progress = false
    $scope.exchange_message = null
    $scope.offer_have_coin_code = $scope.have_coin_code
    $scope.offer_have_coin_count = 123
    $scope.offer_want_coin_code = $scope.want_coin_code
    $scope.offer_want_coin_count = 2321
    $scope.offer_rate = null

    $scope.calcOfferRate = function () {
        $scope.offer_rate = Math.max($scope.offer_have_coin_count / $scope.offer_want_coin_count, $scope.offer_want_coin_count / $scope.offer_have_coin_count)
        $scope.offer_rate = parseFloat($scope.offer_rate.toFixed(4))
    }
    $scope.calcOfferRate();


    $scope.offerHaveCoinCountChange = function () {
        $scope.calcOfferRate()
        $scope.offer_want_coin_count = Math.ceil($scope.offer_have_coin_count * $scope.offer_rate)
        $scope.calcOfferRate()
    }

    $scope.offerWantCoinCountChange = function () {
        $scope.calcOfferRate()
    }

    $scope.offerRateChange = function () {
        $scope.offerHaveCoinCountChange();
    }

    $scope.exchange = function () {
        $scope.exchange_message = null
        $scope.exchange_in_progress = true
        $http.post(api_url + "exchange.php", {
            token: token,
            have_coin_code: $scope.offer_have_coin_code,
            have_coin_count: $scope.offer_have_coin_count,
            want_coin_code: $scope.offer_want_coin_code,
            want_coin_count: $scope.offer_want_coin_count,
        }).then(function (response) {
            $scope.exchange_in_progress = false
            if (response.data.message == null) {
                $scope.offer_have_coin_count = null;
                $scope.offer_want_coin_count = null
                $scope.toggleExchangeFragment = true;
                $scope.toggleExchangeFragment = true;
                updateData();
            } else
                $scope.exchange_message = response.data.message
        })
    };

    $scope.convertRate = function (offer_rate, offer_rate_inverse) {
        var rate = Math.max(parseFloat(offer_rate), parseFloat(offer_rate_inverse));
        return (rate < 1) ? rate.toFixed(4) : rate.toFixed(2);
    }

    $scope.exchangeSwapCurrencies = function () {
        $scope.exchange_message = null;
        if ($scope.getHaveCoin($scope.offer_want_coin_code) != null) {
            var buf_coin_code = $scope.offer_have_coin_code
            var buf_coin_count = $scope.offer_have_coin_count
            $scope.offer_have_coin_code = $scope.offer_want_coin_code;
            $scope.offer_have_coin_count = $scope.offer_want_coin_count;
            $scope.offer_want_coin_code = buf_coin_code
            $scope.offer_want_coin_count = buf_coin_count
        } else {
            $scope.exchange_message = "you dont have " + $scope.offer_want_coin_code;
        }
    }

    $scope.send_message = null
    $scope.send_request_in_progress = false
    $scope.send_wallet_id = null
    $scope.send_coin_code = null
    $scope.send_coin_count = null

    $scope.send = function () {
        $scope.send_message = null
        $scope.send_request_in_progress = true
        $http.post(api_url + "send.php", {
            token: token,
            receiver_user_login: $scope.send_wallet_id,
            coin_code: $scope.send_coin_code,
            coin_count: $scope.send_coin_count,
        }).then(function (response) {
            $scope.send_request_in_progress = false
            if (response.data.message == null) {
                $scope.send_coin_count = null
                $scope.toggleSendFragment = true;
                updateData();
            } else
                $scope.send_message = response.data.message;
        })
    }

    $scope.getHaveCoin = function (coin_code) {
        if ($scope.have_coins != null)
            for (let i = 0; i < $scope.have_coins.length; i++)
                if ($scope.have_coins[i]["coin_code"] === coin_code)
                    return $scope.have_coins[i];
        return null;
    }

})