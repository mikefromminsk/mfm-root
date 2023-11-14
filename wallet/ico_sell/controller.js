function openIcoSell(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico_sell/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.domain = locals.domain
            if (DEBUG) {
                $scope.amount = 100
                $scope.price = 3
            }
            $scope.ico_sell = function () {
                wallet.calcKey(domain + "/wallet", function (key, hash, username) {
                    postContractWithGas(domain, data10.ico_sell, {
                        address: username,
                        key: key,
                        next_hash: hash,
                        amount: $scope.amount,
                        price: $scope.price,
                    }, function () {
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