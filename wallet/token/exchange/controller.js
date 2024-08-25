function openExchange(domain, is_sell) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/token/exchange/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain

            $scope.is_sell = is_sell == 1
            $scope.price = 5
            $scope.amount = 5
            $scope.total
            $scope.availableCoin
            $scope.availableUsdt
            $scope.showChart = false

            $scope.showChartToggle = function () {
                $scope.showChart = !$scope.showChart
            }

            $scope.changePrice = function () {
                if ($scope.price != null && $scope.amount != null)
                    $scope.total = round($scope.price * $scope.amount, 4)
            }


            $scope.changeAmount = function () {
                if ($scope.price != null && $scope.amount != null)
                    $scope.total = round($scope.price * $scope.amount, 4)
            }


            $scope.changeTotal = function () {
                if ($scope.price != null && $scope.total != null)
                    $scope.amount = round($scope.total / $scope.price, 2)
            }

            $scope.place = function () {
                getPin(function (pin) {
                    calcPass($scope.is_sell ? domain : "usdt", pin, function (pass) {
                        postContract("token", "place.php", {
                            domain: domain,
                            is_sell: $scope.is_sell ? 1 : 0,
                            address: wallet.address(),
                            price: $scope.price,
                            amount: $scope.amount,
                            pass: pass
                        }, function () {
                            loadOrders()
                            showSuccess("Order placed", loadOrderbook)
                        })
                    })
                })
            }

            $scope.orders = []

            function loadOrders() {
                postContract("token", "orders.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.orders = []
                    $scope.orders.push.apply($scope.orders, response.active)
                    $scope.orders.push.apply($scope.orders, response.history)
                    $scope.$apply()
                })
            }

            function init() {
                loadProfile()
                loadOrderbook()
                initChart()
                loadOrders()
            }

            function loadProfile() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.token = response
                    $scope.$apply()
                })
            }

            function loadOrderbook() {
                postContract("token", "orderbook.php", {
                    domain: domain,
                }, function (response) {
                    $scope.sell = response.sell
                    $scope.buy = response.buy
                    $scope.orderbook = response
                    $scope.$apply()
                })
            }

            $scope.loadOrderbook = loadOrderbook;

            /*setInterval(function () {
                loadOrderbook()
                //$scope.setPeriod($scope.period_name)
            }, 3000)*/

            var candleSeries

            function initChart() {
                setTimeout(function () {
                    if (candleSeries == null) {
                        candleSeries = createChart("tradeChart").addCandlestickSeries();
                    }
                    $scope.setPeriod($scope.period_name)
                })
            }

            $scope.periods = ['1M', '1H', '1D', '1W']
            $scope.period_name = '1M'
            $scope.setPeriod = function (period_name) {
                $scope.period_name = period_name
                postContract("data", "candles.php", {
                    key: domain + "_price",
                    period_name: period_name,
                }, function (response) {
                    for (var i = 0; i < response.candles.length; i++) {
                        response.candles[i].time =
                            new Date(response.candles[i].time * 1000 + (i * 60 * 60 * 24 * 1000)).toJSON().slice(0, 10)
                    }
                    candleSeries.setData(response.candles)
                    $scope.price = response.value
                    $scope.change24 = response.change24
                });
            }

            $scope.openSettings = function () {
                openSettings(domain);
            }

            init()
        }
    }).then(function () {
        if (success)
            success()
    })


}