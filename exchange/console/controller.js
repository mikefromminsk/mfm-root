function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    var domain = $scope.getUriParam("domain")
    $scope.domain = domain

    $scope.is_sell = false
    $scope.price = 5
    $scope.amount = 5
    $scope.total
    $scope.availableCoin
    $scope.availableUsdt
    $scope.showChart = false

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

    $scope.init = function () {
        postContractWithGas(domain, "api/exchange/init.php", {}, function (response) {
            console.log(response.result)
        })
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

    function init() {
        loadProfile()
        loadOrderbook()
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
        postContract(domain, "api/exchange/orderbook.php", {
        }, function (response) {
            $scope.sell = response.sell
            $scope.buy = response.buy
            $scope.orderbook = response
            $scope.$apply()
        })
    }

    /*setInterval(function () {
        loadOrderbook()
    }, 3000)*/




    init()
}