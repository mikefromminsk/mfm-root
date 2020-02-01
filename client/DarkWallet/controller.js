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
    $scope.toggleCreateCoinFragment = false;

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

    $scope.walletId = login;

    if (login == null || token == null || stock_token == null)
        $scope.open('login');
    else {
        updateData();
    }

    function updateData() {
        $http.post(api_url + "wallet.php", {
            token: token,
        }).then(function (response) {
            if (response.data.message == null) {
                $scope.coins = response.data.coins;
                $scope.have_coins = response.data.have_coins;
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
    $scope.have_coin_code = "FTR"
    $scope.want_coin_code = "WEF"
    $scope.offer_have_coin_code = $scope.have_coin_code
    $scope.offer_have_coin_count = null;
    $scope.offer_want_coin_code = $scope.want_coin_code
    $scope.offer_want_coin_count = null
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
                updateData();
            } else
                $scope.exchange_message = response.data.message
        })
    };

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

    $scope.maxSendCoinCount = function () {
        if ($scope.have_coins != null)
            for (let i = 0; i < $scope.have_coins.length; i++)
                if ($scope.have_coins[i]["coin_code"] === $scope.send_coin_code)
                    return $scope.have_coins[i]["coin_count"];
        return 0;
    }

})