function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    var domain = getParam("domain")
    var is_sell = getParam("is_sell")
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
        if ($scope.is_sell == true) {
            postContractWithGas(domain, "api/exchange/place.php", function (key, next_hash) {
                return {
                    is_sell: 1,
                    address: wallet.address(),
                    price: $scope.price,
                    amount: $scope.amount,
                    key: key,
                    next_hash: next_hash,
                }
            }, function () {
                loadOrders()
                showSuccessDialog("Order placed", loadOrderbook)
            })
        } else {
            postContractWithGas("usdt", "api/exchange/place.php", function (key, next_hash) {
                postContractWithGas(domain, "api/exchange/place.php", {
                    is_sell: 0,
                    address: wallet.address(),
                    price: $scope.price,
                    amount: $scope.amount,
                    key: key,
                    next_hash: next_hash,
                }, function () {
                    showSuccessDialog("Order placed", loadOrderbook)
                })
            })
        }
    }

    $scope.orders = []

    function loadOrders() {
        postContract("exchange", "utils/orders.php", {
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
            $scope.coin = response
            $scope.$apply()
        })
    }

    function loadOrderbook() {
        postContract(domain, "utils/orderbook.php", {}, function (response) {
            $scope.sell = response.sell
            $scope.buy = response.buy
            $scope.orderbook = response
            $scope.$apply()
        })
    }

    $scope.loadOrderbook = loadOrderbook;

    setInterval(function () {
        loadOrderbook()
        loadOrders()
        $scope.setPeriod($scope.period_name)
    }, 3000)


    var candleSeries

    function initChart() {
        setTimeout(function () {
            if (candleSeries == null) {
                candleSeries = createChart("tradeChart").addCandlestickSeries();
            }
            $scope.setPeriod($scope.period_name)
        })
    }

    $scope.periods = ['1M', '1H', '1D', '1Y']
    $scope.period_name = '1M'
    $scope.setPeriod = function (period_name) {
        $scope.period_name = period_name
        postContract("exchange", "utils/candles.php", {
            domain: domain,
            key: "price",
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