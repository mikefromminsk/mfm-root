function openIcoBuy(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico_buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            if (DEBUG) {
                $scope.amount = 1
            }
            postContract(domain, data10.wallet, {
                address: wallet.username
            }, function (response) {
                $scope.balance = response.amount
            })

            post("/data/api/get.php", {
                path: domain + "/price"
            }, function (response) {
                $scope.price = response
                $scope.$apply()
            })

            $scope.ico_buy = function () {
                wallet.calcKey("usdt/wallet", function (key, hash, username) {
                    postContractWithGas(domain, data10.ico_buy, {
                        address: username,
                        key: key,
                        next_hash: hash,
                        amount: $scope.amount,
                    }, function () {
                        success()
                        $mdBottomSheet.hide()
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}