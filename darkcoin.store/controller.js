controller("darkcoin.store", function ($scope, $window, $http, $interval, $routeParams) {
    $scope.hostname = window.location.hostname
    $scope.activeWindow = 0;
    $scope.windowWidth = $window.innerWidth;
    angular.element($window).bind('resize', function () {
        $scope.windowWidth = $window.innerWidth;
        $scope.$apply();
    });

    $scope.toggleSendFragment = true
    $scope.toggleExchangeFragment = true
    $scope.toggleCreateCoinFragment = true

    $scope.show = function (fragmentName) {
        $scope.toggleSendFragment = !$scope.toggleSendFragment || fragmentName !== "send";
        $scope.toggleExchangeFragment = !$scope.toggleExchangeFragment || fragmentName !== "exchange";
        $scope.toggleCreateCoinFragment = !$scope.toggleCreateCoinFragment || fragmentName !== "create_coin";
        if (!$scope.toggleSendFragment)
            $scope.activeWindow = 0;
        if (!$scope.toggleExchangeFragment)
            $scope.activeWindow = 1;
        if (!$scope.toggleCreateCoinFragment)
            $scope.activeWindow = 2;
    }


    $scope.user_login = store.get("user_login");
    $scope.token = store.get("user_session_token") || $routeParams.arg0;

    $scope.have_coin_code = null
    $scope.want_coin_code = null

    if ($scope.token != null){
        updateData()
        startTimer()
    }

    $scope.toggleLoginReg = true

    $scope.login = null
    $scope.password = null
    $scope.agreeWithTeems = null
    $scope.login_message = null

    $scope.login_request_in_progress = false;
    $scope.loginButton = function () {
        $scope.login_request_in_progress = true
        store.clear()
        $http.post(pathToRootDir + "darkcoin.store/api/login_check.php", {
            token: $scope.token,
            user_login: $scope.login,
            user_password: $scope.password,
        })
            .then(function (response) {
                $scope.login_request_in_progress = false
                $scope.password = null
                if (response.data.message == null) {
                    $scope.user_login = response.data.user_login;
                    $scope.token = response.data.user_session_token
                    store.set("user_login", response.data.user_login)
                    store.set("user_session_token", response.data.user_session_token)
                    store.set("user_stock_token", response.data.user_stock_token)
                    startTimer()
                } else {
                    $scope.login_message = response.data.message
                    if ($scope.agreeWithTeems && $scope.login_message.indexOf("verify"))
                        $scope.toggleLoginReg = true;
                }
            }, function () {
                $scope.login_request_in_progress = false
                alert("Connection failed to server")
            })
    }

    $scope.logout = function () {
        store.clear()
        $scope.token = null
    }

    $scope.copyButtonLabel = "Copy Login"
    $scope.copyToClipboard = function (text) {
        var copyText = document.getElementById("walletId");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        copyText.setSelectionRange(0, 0);
        copyText.blur();
        $scope.copyButtonLabel = 'coped';
    }

    $scope.stock_fee_in_rub = null;

    function updateData(coin_code) {
        $http.post(pathToRootDir + "darkcoin.store/api/wallet.php", {
            token: $scope.token,
        }).then(function (response) {
            if (response.data.message == null) {
                $scope.coins = response.data.coins;
                $scope.have_coins = response.data.have_coins;
                $scope.have_coin_code = coin_code || $scope.coins[0]["coin_code"] || $scope.have_coins[0]["coin_code"]
                $scope.want_coin_code = "USD"
                $scope.exchange_server_script = response.data.exchange_server_script
                $scope.stock_fee_in_rub = response.data.stock_fee_in_rub;

                $http.post($scope.exchange_server_script, {
                    stock_token: store.get("user_stock_token"),
                    have_coin_code: $scope.have_coin_code,
                    want_coin_code: $scope.want_coin_code,
                }).then(function (response) {
                    if (response.data.message == null) {
                        $scope.saleOffers = response.data.sale_offers;
                        $scope.buyOffers = response.data.buy_offers;
                        $scope.haveOffers = response.data.have_offers;
                        $scope.rates = response.data.rates;

                        $scope.offer_have_coin_code = $scope.have_coin_code
                        $scope.offer_want_coin_code = $scope.want_coin_code
                        setBaseOffer()
                    }
                })
            }
        })
    }

    $scope.getRate = function (coin_code) {
        if ($scope.rates != null)
            for (var i = 0; i < $scope.rates.length; i++)
                if ($scope.rates[i].coin_code === coin_code)
                    return parseFloat($scope.rates[i].offer_rate);
        return null;
    }


    $scope.calcRate = function () {
        $scope.offer_rate = $scope.round($scope.offer_want_coin_count / $scope.offer_have_coin_count)
    }

    $scope.create_coin_message = null
    $scope.create_coin_request_in_progress = false
    $scope.newCoinName = ""
    $scope.newCoinCode = ""
    $scope.agreeWithPublicationOnExchangeStock = false
    $scope.createCoin = function () {
        $scope.create_coin_message = null
        $scope.create_coin_request_in_progress = true;
        $http.post(pathToRootDir + "darkcoin.store/api/coin_create.php", {
            token: $scope.token,
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
    $scope.offer_have_coin_code = null
    $scope.offer_have_coin_count = null
    $scope.offer_want_coin_code = null
    $scope.offer_want_coin_count = null
    $scope.offer_rate = null

    $scope.offerHaveCoinCountChange = function () {
        $scope.calcRate()
    }

    $scope.offerWantCoinCountChange = function () {
        $scope.calcRate()
    }

    $scope.offerRateChange = function () {
        $scope.offer_want_coin_count = $scope.offer_have_coin_count * $scope.offer_rate
    }

    function setBaseOffer() {
        var rate = $scope.getRate($scope.have_coin_code)
        if (rate < 1) {
            $scope.offer_have_coin_count = Math.round(1 / rate)
            $scope.offer_want_coin_count = 1
            $scope.calcRate()
        } else {
            $scope.offer_have_coin_count = 1
            $scope.offer_want_coin_count = Math.round(rate)
            $scope.calcRate()
        }
    }

    $scope.exchange = function () {
        $scope.exchange_message = null
        $scope.exchange_in_progress = true
        $http.post(pathToRootDir + "darkcoin.store/api/exchange.php", {
            token: $scope.token,
            have_coin_code: $scope.offer_have_coin_code,
            have_coin_count: $scope.offer_have_coin_count,
            want_coin_code: $scope.offer_want_coin_code,
            want_coin_count: $scope.offer_want_coin_count,
        }).then(function (response) {
            $scope.exchange_in_progress = false
            if (response.data.message == null) {
                $scope.toggleExchangeFragment = true
                updateData($scope.offer_have_coin_code)
            } else
                $scope.exchange_message = response.data.message
        })
    };

    $scope.round = function (rate) {
        if (typeof rate == "string")
            rate = parseFloat(rate)
        if (rate > 1)
            if (rate > 0.0001 && rate < 1)
                return parseFloat(rate.toFixed(2))
        if (rate > 0.0001 && rate < 1)
            return parseFloat(rate.toFixed(4))
        return parseFloat(rate.toFixed(8))
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

    $scope.openCoin = function (coin_code) {
        if (coin_code !== "USD") {
            updateData(coin_code);
            $scope.activeWindow = 1;
        }
    }

    $scope.send_message = null
    $scope.send_request_in_progress = false
    $scope.send_user_login = null
    $scope.send_coin_code = null
    $scope.send_coin_count = null

    $scope.send = function () {
        $scope.send_message = null
        $scope.send_request_in_progress = true
        $http.post(pathToRootDir + "darkcoin.store/api/send.php", {
            token: $scope.token,
            receiver_user_login: $scope.send_user_login,
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

    $scope.openCreateCoinFrame = function () {
        $scope.toggleSendFragment = true
        $scope.toggleExchangeFragment = true
        $scope.toggleCreateCoinFragment = false
    }

    $scope.messages = null;
    $scope.message_index = 0;
    var messagesInterval;
    function startTimer() {
        messagesInterval = $interval(function () {
            $http.post(pathToRootDir + "darkcoin.store/api/messages.php", {
                token: $scope.token,
            }).then(function (response) {
                if (response.data.messages != null) {
                    $scope.messages = response.data.messages;
                    $scope.message_index = 0;
                    updateData();
                }
            })
        }, 1000);
    }

    function stopTimer() {
        if (angular.isDefined(messagesInterval))
            $interval.cancel(messagesInterval);
    }

    $scope.$on('$destroy', function () {
        stopTimer();
    });
})