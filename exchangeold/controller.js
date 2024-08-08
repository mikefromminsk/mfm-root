var App = angular.module("App", ['ngMaterial', 'ngAnimate'])

var token = "321" || localStorage.getItem('token')
var night_mode = localStorage.getItem('night_mode') == 'dark'

App.config(function ($mdThemingProvider) {
    var theme = $mdThemingProvider.theme('default')
        .accentPalette('indigo')
    if (night_mode)
        theme.dark()
    $mdThemingProvider.enableBrowserColor({
        theme: 'default',
        palette: 'primary',
    })
});

App.controller("Controller", function ($scope, $http, $mdBottomSheet, $mdToast, $mdTheming) {
    $scope.str = str
    var orderbookTimer = null;
    var ticker = null || "SOL"
    if (token == null)
        localStorage.setItem('token', token = Math.random())

    $scope.night_mode = night_mode
    $scope.selectNightMode = function () {
        $scope.night_mode = !$scope.night_mode
        localStorage.setItem('night_mode', $scope.night_mode ? 'dark' : null)
        location.reload()
    }

    $scope.share = function (link) {
        navigator.clipboard.writeText(link)
        $mdToast.show($mdToast.simple().textContent(str.copied))
    }

    $scope.tariffs = [
        {
            title: str.basic,
            description: str.basic_desc,
            supply: 10000,
            price: 10,
        }, {
            title: str.pro,
            description: str.pro_desc,
            supply: 100000,
            price: 100,
        }, {
            title: str.custom,
            description: str.custom_desc,
            supply: 10000,
            price: 10,
        }
    ]
    $scope.tariffIndex = 0
    $scope.selectTariff = function (index) {
        $scope.tariffIndex = index
    }

    $scope.launchCoin = {
        ticker: "",
        name: "",
        description: "",
        supply: 10000,
        price: 0.2,
        usd_round: 0,
        starter_supply: 1000,
    }

    $scope.generateStarterLogo = function () {
        var svg = new UIAvatarSvg()
            .text(($scope.launchCoin.name || "B")[0].toUpperCase())
            .round(true)
            .size(64)
            .bgColor('#' + Math.floor(Math.random() * 16777215).toString(16))
            .textColor('#ffffff')
            .fontSize(0.8)
            .fontWeight('normal')
            .fontFamily('-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Sans', 'Droid Sans', 'Helvetica Neue', 'sans-serif')
            .generate()
        $scope.launchCoin.logo = new Blob([svg], {type: 'image/svg+xml'})
        document.getElementById('starter-logo').src = URL.createObjectURL($scope.launchCoin.logo)
    }

    $scope.validate = function (type, value, success, error) {
        $http({
            method: "POST",
            url: "api/validate.php",
            data: {
                type: type,
                value: value,
            }
        }).then(success, error)
    }

    $scope.changeCoinName = function () {
        $scope.validate('coin_name', $scope.launchCoin.name, function () {
            $scope.coin_name_message = null
        }, function (response) {
            $scope.coin_name_message = response.data.message
        })
        if ($scope.launchCoin.name != null) {
            $scope.launchCoin.ticker = $scope.launchCoin.name.substr(0, 3).toUpperCase()
            $scope.changeCoinTicker()
        } else
            $scope.launchCoin.ticker = null
        $scope.generateStarterLogo()
    }

    $scope.changeCoinTicker = function () {
        $scope.validate('coin_ticker', $scope.launchCoin.ticker, function () {
            $scope.coin_ticker_message = null
        }, function (response) {
            $scope.coin_ticker_message = response.data.message
        })
    }

    $scope.$watch(function () {
        return $scope.launchCoin.usd_round
    }, function (newValue) {
        $scope.launchCoin.price = newValue / ($scope.launchCoin.supply * 0.1)
    })

    $scope.launch = function () {
        $scope.launchCoin.token = token
        $http({
            method: 'POST',
            url: 'api/create_coin.php',
            headers: {
                'Content-Type': undefined
            },
            data: $scope.launchCoin,
            transformRequest: objectToForm
        }).then(function (response) {
            if (response.data.result) {
                var loadUser = false
                updateUser(function () {
                    loadUser = true
                    attemptToClose()
                })
                var loadCoins = false
                updateCoins(function () {
                    loadCoins = true
                    attemptToClose()
                })

                function attemptToClose() {
                    $scope.selected
                    $scope.starterIndex = 1
                }
            }
        })
    }


    $scope.helloImages = strArray('hello_img')
    $scope.helloLabels = strArray('hello_label')
    $scope.helloTitles = strArray('hello_title')
    $scope.helloTexts = strArray('hello_text')

    $scope.showHello = localStorage.getItem('hello_showed') == null
    $scope.helloIndex = 0
    $scope.helloNext = function () {
        if ($scope.helloIndex == $scope.helloLabels.length - 1) {
            $scope.showHello = false
            localStorage.setItem('hello_showed', '1')
        } else
            $scope.helloIndex++
    }

    $scope.selectBalance = function (balance) {
        $mdBottomSheet.show({
            templateUrl: 'balance.html',
            //scope: $scope.$new(),
            locals: {
                balance: balance
            },
            controller: function ($scope, $mdBottomSheet, locals) {
                $scope.locals = locals
                $scope.hide = function (choose) {
                    $mdBottomSheet.hide(choose)
                }
            }
        }).then(function (choose) {
            if (choose == 'trade') {
                $scope.openTrade(balance.ticker)
            } else if (choose == 'withdrawal') {
                $scope.openWithdrawal(balance)
            } else if (choose == 'deposit') {
                $scope.openDeposit(balance.ticker)
            }
        })
    }

    $scope.openDeposit = function (ticker) {
        $mdBottomSheet.show({
            templateUrl: 'deposit.html',
            //scope: $scope.$new(),
            locals: {
                ticker: ticker,
                balance: $scope.balances[ticker],
            },
            controller: function ($scope, $mdBottomSheet, locals) {
                $scope.locals = locals
                $scope.selectFile = function () {
                    selectFile('json', function (file) {
                        $scope.in_progress = true
                        $http({
                            method: 'POST',
                            url: 'api/deposit.php',
                            headers: {
                                'Content-Type': undefined
                            },
                            data: {
                                token: token,
                                file: file
                            },
                            transformRequest: objectToForm
                        }).then(function (response) {
                            updateUser()
                            $scope.in_progress = false
                            $mdBottomSheet.hide()
                        })
                    })
                }
            }
        })
    }

    $scope.openWithdrawal = function (balance) {
        $mdBottomSheet.show({
            templateUrl: 'withdrawal.html',
            //scope: $scope.$new(),
            locals: {
                balance: balance
            },
            controller: function ($scope, $mdBottomSheet, locals) {
                $scope.locals = locals
                $scope.withdraw = function () {
                    download(balance.ticker + $scope.amount + ".json",
                        "api/withdrawal.php?token=" + token + "&ticker=" + balance.ticker + "&amount=" + $scope.amount)
                    $mdBottomSheet.hide()
                }
            }
        })
    }

    function initDrops() {
        $http({
            method: "POST",
            url: "api/drops.php",
            data: {
                token: token,
            }
        }).then(function (response) {
            $scope.drops = response.data.drops
        })
    }

    $scope.openDrop = function (drop) {
        if (drop.type == "SIMPLE") {
            $http({
                method: "POST",
                url: "api/drop_finish.php",
                data: {
                    token: token,
                    drop_id: drop.drop_id,
                }
            }).then(function (response) {
                initDrops()
            })
        }
    }

    var stakingTimer;

    function initStaking() {
        stakingTimer = setInterval(stakingTimerAction, 1000)

        updateUser()

        $http({
            method: "POST",
            url: "api/stakes.php",
            data: {
                token: token,
            }
        }).then(function (response) {
            $scope.stakes = response.data.stakes
        })

        updateCoins(function () {
            $scope.stakingActive = []
            for (var key in $scope.coins)
                if ($scope.coins[key].staking_apy > 0)
                    $scope.stakingActive.push($scope.coins[key])
        })
    }

    function stakingTimerAction() {
        for (var key in $scope.stakes) {
            var stake = $scope.stakes[key]
            stake.earned = stake.amount * (new Date().getTime() / 1000 - stake.time) / (1000 * 60 * 60 * 24 * 365) * (stake.parameter / 100)
        }
        $scope.$apply()
    }

    $scope.selectStaking = function (item) {
        $scope.selectedStaking = item
    }

    $scope.stake = function () {
        $http({
            method: "POST",
            url: "api/stake.php",
            data: {
                token: token,
                ticker: $scope.selectedStaking.ticker,
                amount: $scope.stakingAmount,
            }
        }).then(function (response) {
            $scope.selectedStaking = null
            initStaking()
        })
    }

    $scope.unstake = function (item) {
        $http({
            method: "POST",
            url: "api/stake_close.php",
            data: {
                token: token,
                stake_id: item.transfer_id,
            }
        }).then(function (response) {
            initStaking()
        })
    }


    $scope.openTrade = function (ticker) {
        $scope.coin = $scope.coins[ticker]
        $scope.marketIndex = 1
        $scope.selectMenu(0)
    }


    function updateStarterList() {
        $http({
            method: "POST",
            url: "api/ieo.php",
            data: $scope.launchCoin
        }).then(function (response) {
            $scope.ieo = response.data.ieo
            if ($scope.selectedStarter == null) {
                $scope.selectedStarter = $scope.ieo[0]
            } else {
                for (var key in $scope.ieo)
                    if ($scope.ieo[key].ticker == $scope.selectedStarter.ticker)
                        $scope.selectedStarter = $scope.ieo[key]
            }
        })
    }

    $scope.selectedStarter
    $scope.selectStarter = function (starterCoin) {
        $scope.selectedStarter = starterCoin
        $scope.starterIndex = 2
    }

    $scope.backProject = function () {
        $scope.order(str.back_project + ' ' + $scope.selectedStarter.name, 'USDT', 10, false, function (usdt) {
                $http({
                    method: "POST",
                    url: "api/place.php",
                    data: {
                        token: token,
                        ticker: $scope.selectedStarter.ticker,
                        is_sell: "0",
                        price: $scope.selectedStarter.price,
                        amount: usdt / $scope.selectedStarter.price
                    }
                }).then(function (response) {
                    updateStarterList()
                })
            }
        )
    }


    $scope.sellLineClick = function (index) {
        $scope.is_sell = false
        $scope.amount = 0
        for (var i = $scope.sell.length - 1; i >= index; i--)
            $scope.amount += $scope.sell[i].amount
        $scope.amount = round($scope.amount, 2)
        $scope.price = round($scope.sell[index].price, 2)
        $scope.changeAmount()
    }

    $scope.buyLineClick = function (index) {
        $scope.is_sell = true
        $scope.amount = 0
        for (var i = 0; i <= index; i++)
            $scope.amount += $scope.buy[i].amount
        $scope.amount = round($scope.amount, 2)
        $scope.price = round($scope.buy[index].price, 2)
        $scope.changeAmount()
    }

    function initTrade() {
        trackTradeViewed()
        $scope.availableCoin = $scope.balances[$scope.coin.ticker] ? $scope.balances[$scope.coin.ticker].spot : 0
        $scope.availableUsdt = $scope.balances["USDT"].spot
        updateOrderbook();
        orderbookTimer = setInterval(updateOrderbook, 1000)
        updateOrders();
        initChart()
    }

    $scope.home_button_titles = strArray('home_button_title')
    $scope.home_button_imgs = strArray('home_button_img')
    $scope.home_button_action = function (action) {
        if (action == 'deposit') {
            $scope.selectMenu(3)
        } else if (action == 'withdrawal') {
            $scope.selectMenu(3)
        } else if (action == 'starter') {
            $scope.selectMenu(1)
        }
    }

    $scope.openCoin = function (newTicker) {
        ticker = newTicker
        $scope.coin = $scope.coins[ticker]
        $scope.marketIndex = 1
    }

    $scope.showChart = true;
    $scope.showChartToggle = function () {
        $scope.showChart = !$scope.showChart
    }
    var chart
    var candleSeries

    function initChart() {
        setTimeout(function () {
            if (chart == null) {
                var tradeChart = document.getElementById("tradeChart")
                chart = LightweightCharts.createChart(tradeChart, {
                    crosshair: {
                        mode: LightweightCharts.CrosshairMode.Normal,
                    },
                    timeScale: {
                        timeVisible: true,
                    },
                });
                candleSeries = chart.addCandlestickSeries();
                /*new ResizeObserver(entries => {
                    if (entries.length === 0 || entries[0].target !== tradeChart) return;
                    const newRect = entries[0].contentRect;
                    chart.applyOptions({height: newRect.height, width: newRect.width});
                }).observe(tradeChart)*/
            }
            $scope.getSticks($scope.period)
        })
    }


    $scope.periods = ['1m', '5m', '15m', '1H', '1D']
    $scope.period = '1s'
    $scope.getSticks = function (period) {
        $scope.period = period
        if (candleSeries != null)
            $http({
                method: "POST",
                url: "api/candles.php",
                data: {
                    ticker: ticker,
                    period: $scope.period
                }
            }).then(function (response) {
                candleSeries.setData(response.data.candles)
            })
    }

    var currentBar = {}

    function updateChart() {
        if (chart != null) {
            var price = parseFloat($scope.coin.price)
            var nextTime = Math.ceil(new Date() / 1000 / 60) * 60
            if (nextTime != currentBar.time) {
                currentBar.open = currentBar.close || price
                currentBar.high = price
                currentBar.low = price
                currentBar.close = price
                currentBar.time = nextTime
            } else {
                currentBar.close = price
                currentBar.high = Math.max(currentBar.high, price)
                currentBar.low = Math.min(currentBar.low, price)
            }
            candleSeries.update(currentBar)
        }
    }

    $scope.setPortion = function (percent) {
        $scope.amount = ($scope.is_sell ? $scope.availableCoin : $scope.availableUsdt) * (percent / 100)
        $scope.changeAmount()
    }

    $scope.place = function () {
        $http({
            method: "POST",
            url: "api/place.php",
            data: {
                token: token,
                ticker: ticker,
                is_sell: $scope.is_sell ? "1" : "0",
                price: $scope.price,
                amount: $scope.amount
            }
        }).then(updateOrdersResult)
    }

    $scope.cancel = function (order_id) {
        $http({
            method: "POST",
            url: "api/cancel.php",
            data: {token: token, order_id: order_id}
        }).then(function (response) {
            updateOrders()
        })
    }
    $scope.cancelAll = function () {
        $http({
            method: "POST",
            url: "api/cancelAll.php",
            data: {token: token, ticker: ticker}
        }).then(function () {
            updateOrders()
        })
    }

    function initOrders() {

    }



    function initMarket() {
        bannerTimer = setInterval(function () {
            $scope.bannerIndex = ($scope.bannerIndex + 1) % $scope.banners.length
            $scope.$apply()
        }, 5000)
        updateUser()
    }

    $scope.banners = strArray('banner')
    $scope.bannerIndex = 0
    var bannerTimer

    $scope.nonzero_coins = [];
    $scope.zero_coins = [];
    $scope.search_text = ""
    $scope.$watch('search_text', function (newValue) {
        filterCoins();
    })

    function filterCoins() {
        if ($scope.coins == null) return
        var filtered_coins;
        if ($scope.search_text == "") {
            filtered_coins = Object.values($scope.coins)
        } else {
            var search = $scope.search_text.toLowerCase()
            filtered_coins = []
            for (var key in $scope.coins) {
                var coin = $scope.coins[key]
                if (coin.name.toLowerCase().indexOf(search) != -1
                    || coin.ticker.toLowerCase().indexOf(search) != -1)
                    filtered_coins.push(coin)
            }
        }

        $scope.nonzero_coins = [];
        $scope.zero_coins = [];
        for (var key in filtered_coins) {
            if ($scope.balances[filtered_coins[key].ticker] != null)
                $scope.nonzero_coins.push(filtered_coins[key]);
            else
                $scope.zero_coins.push(filtered_coins[key]);
        }

        $scope.nonzero_coins.sort(function compare(a, b) {
            let balanceA = $scope.balances[a.ticker]
            let balanceB = $scope.balances[b.ticker]
            return (b.price * (balanceB.spot + balanceB.blocked)) - (a.price * (balanceA.spot + balanceA.blocked));
        })

        $scope.zero_coins.sort(function compare(a, b) {
            return a.price * a.supply - b.price * b.supply;
        })
    }


    function updateOrderbook() {
        $http({
            method: "POST",
            url: "api/orderbook.php",
            data: {ticker: $scope.coin.ticker}
        }).then(function (response) {
            $scope.sell = response.data.sell
            $scope.buy = response.data.buy
            $scope.coin = response.data.coin
            $scope.getSticks($scope.period)
            updateChart()
        })
    }


    function updateOrders() {
        $http({method: "POST", url: "api/orders.php", data: {token: token}}).then(updateOrdersResult)
    }

    $scope.allActiveOrders = []
    $scope.allHistoryOrders = []
    $scope.coinActiveOrders = []
    $scope.coinHistoryOrders = []

    function ordersByTicker(list, ticker) {
        var result = [];
        for (var key in list) {
            var order = list[key]
            if (order.ticker == ticker)
                result.push(order)
        }
        return result
    }

    function updateOrdersResult(response) {
        $scope.allActiveOrders = response.data.active || []
        $scope.allHistoryOrders = response.data.history || []
        $scope.coinActiveOrders = ordersByTicker($scope.allActiveOrders, $scope.coin.ticker)
        $scope.coinHistoryOrders = ordersByTicker($scope.allHistoryOrders, $scope.coin.ticker)
    }

    var numberFormat = new Intl.NumberFormat();
    $scope.priceFormat = function (number) {
        return "$" + numberFormat.format(round(number, 2))
    }
    $scope.amountFormat = function (number) {
        return round(number, 4) // K M B T
    }
    $scope.changeFormat = function (number) {
        if (number < 0)
            return "-" + number + "%";
        else if (number == 0)
            return "0%";
        else if (number > 0)
            return "+" + number + "%";
    }

    $scope.percentColor = function (number) {
        return {'green-text': number > 0, 'red-text': number < 0}
    }

    $scope.formatTime = function (number) {
        return new Date(number * 1000).toLocaleString()
    }

    $scope.percentFormat = function (number) {
        return round(number, 0) + "%";
    }

    $scope.order = function (title, ticker, amount, blockedAmount, callback) {
        $mdBottomSheet.show({
            templateUrl: 'order.html',
            locals: {
                str: str,
                title: title,
                ticker: ticker,
                amount: amount,
                blockedAmount: blockedAmount,
                balance: $scope.balances[ticker]
            },
            controller: function ($scope, $mdBottomSheet, locals) {
                $scope.locals = locals
                $scope.submit = function () {
                    $mdBottomSheet.hide(locals.amount)
                }
            }
        }).then(function (selectedAmount) {
            callback(selectedAmount)
        })
    }

    function clearTimers() {
        if (bannerTimer != null)
            clearInterval(bannerTimer)
        if (orderbookTimer != null)
            clearInterval(orderbookTimer)
        if (walletTimer != null)
            clearInterval(walletTimer)
        if (stakingTimer != null)
            clearInterval(stakingTimer)
    }

    var userLoaded = false
    var coinsLoaded = false

    updateUser(function () {
        userLoaded = true
        attemptInit()
    })

    updateCoins(function () {
        coinsLoaded = true
        attemptInit()
    })

    function attemptInit() {
        if (userLoaded && coinsLoaded) {
            $scope.selectMenu(0)
        }
    }

    function updateUser(callback) {
        $http({method: "POST", url: "api/user.php", data: {token: token}}).then(function (response) {
            $scope.user = response.data.user
            $scope.balances = response.data.balances
            $scope.version = response.data.version
            calcTotal()
            if (callback != null)
                callback()
        })
    }

    $scope.go = function (url) {
        window.open(url, '_blank').focus();
    }


    function calcTotal() {
        $scope.total_usd_balance = 0;
        $scope.total_usd_change24 = 0;
    }

    function updateCoins(callback) {
        $http({method: "POST", url: "api/coins.php"}).then(function (response) {
            $scope.coins = response.data.coins
            $scope.coin = $scope.coins[ticker]
            filterCoins()
            if (callback != null)
                callback()
        })
    }

    var walletTimer

    function initWallet() {
        $scope.search_text = ""
        filterCoins()
        walletTimer = setInterval(updateUser, 1000)
    }

    $scope.login = function () {
        $mdBottomSheet.show({
            templateUrl: 'login.html',
            locals: {
                user: $scope.user,
                str: str,
                balance: $scope.balances[ticker]
            },
            controller: function ($scope, $mdBottomSheet, locals) {
                $scope.locals = locals

                $scope.sendEmailCode = function () {
                    $http({
                        method: "POST",
                        url: "api/email_send_code.php",
                        data: {
                            token: token,
                            email: $scope.locals.user.email,
                        }
                    }).then(function (response) {
                        if (response.data.result)
                            $scope.locals.user.email_confirmed = 0
                    })
                }

                $scope.confirmEmailCode = function () {
                    $http({
                        method: "POST",
                        url: "api/email_confirm.php",
                        data: {
                            token: token,
                            email_confirm_code: $scope.locals.user.email_confirm_code,
                        }
                    }).then(function (response) {
                        if (response.data.result == true)
                            $mdBottomSheet.hide("success")
                    })
                }
            }
        }).then(function (result) {
            if (result != null)
                updateUser()
        })
    }


    $scope.transaction_history = []

    function initTransactions() {
        $http({method: "POST", url: "api/transactions.php", data: {token: token}}).then(function (response) {
            $scope.transaction_history = response.data.transaction_history
        })
    }


    $scope.menu = [str.trade, str.starter, str.earn, str.wallet]
    $scope.menuIndex
    $scope.selectMenu = function (index) {
        $scope.menuIndex = index
    }

    $scope.marketIndex

    $scope.starterIndex

    $scope.earnIndex

    $scope.walletIndex

    $scope.$watch('menuIndex', function (newValue, oldValue) {
        switch (newValue) {
            case 0:
                showMarket(0)
                break
            case 1:
                showStarter(0)
                break
            case 2:
                showEarn(0)
                break
            case 3:
                showWallet(0)
                break
        }
    })

    $scope.$watch('marketIndex', function (newValue, oldValue) {
        if (oldValue == null || newValue == null) return;
        showMarket(newValue)
    })

    function showMarket(index) {
        clearTimers()
        switch (index) {
            case 0:
                initMarket();
                break
            case 1:
                initTrade();
                break
        }
    }

    $scope.$watch('starterIndex', function (newValue, oldValue) {
        if (oldValue == null || newValue == null) return;
        showStarter(newValue)
    })

    function showStarter(index) {
        clearTimers()
        $scope.starterIndex = index
        switch (index) {
            case 1:
                updateStarterList();
                $scope.selectedStarter = $scope.ieo[0]
                break
        }
        $scope.generateStarterLogo()
    }

    $scope.$watch('earnIndex', function (newValue, oldValue) {
        if (oldValue == null || newValue == null) return;
        showEarn(newValue)
    })

    function showEarn(index) {
        clearTimers()
        $scope.earnIndex = index
        switch (index) {
            case 0:
                initDrops();
                break
            case 1:
                initStaking();
                break
        }
    }

    $scope.$watch('walletIndex', function (newValue, oldValue) {
        if (oldValue == null || newValue == null) return;
        showWallet(newValue)
    })

    function showWallet(index) {
        clearTimers()
        $scope.walletIndex = index
        switch (index) {
            case 0:
                initWallet();
                break;
            case 1:
                initTransactions();
                break;
        }
    }
})