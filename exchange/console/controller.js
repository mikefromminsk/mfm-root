function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    var domain = $scope.getUriParam("domain")
    $scope.domain = domain
    $scope.is_sell = 1
    $scope.price = 5
    $scope.amount = 5

    $scope.init = function () {
        postContractWithGas(domain, "api/exchange/init.php", {}, function (response) {
            console.log(response.result)
        })
    }

    $scope.place = function () {
        if ($scope.is_sell == 1) {
            postContractWithGas(domain, "api/exchange/place.php", function (key, next_hash) {
                return {
                    is_sell: $scope.is_sell,
                    address: wallet.address(),
                    price: $scope.price,
                    amount: $scope.amount,
                    key: key,
                    next_hash: next_hash,
                }
            }, function () {
                loadOrderbook()
            })
        } else {
            postContractWithGas("usdt", "api/exchange/place.php", function (key, next_hash) {
                postContractWithGas(domain, "api/exchange/place.php", {
                    is_sell: $scope.is_sell,
                    address: wallet.address(),
                    price: $scope.price,
                    amount: $scope.amount,
                    key: key,
                    next_hash: next_hash,
                }, function () {
                    loadOrderbook()
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
        postContract(domain, "api/exchange/orderbook.php", {}, function (response) {
            $scope.orderbook = response
            $scope.$apply()
        }, function () {

        })
    }

    /*setInterval(function () {
        loadOrderbook()
    }, 3000)*/

    init()
}