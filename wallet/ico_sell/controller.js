function openIcoSell(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico_sell/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.domain = locals.domain
            if (DEBUG) {
                $scope.amount = 1000000
                $scope.price = 16
            }
            $scope.ico_sell = function () {
                wallet.auth(function (username) {
                    postContract(domain, data10.ico_sell, {
                        address: username,
                        key: null,
                        next_hash: wallet.calchashStart(domain + "/wallet/ico"),
                        amount: $scope.amount,
                        price: $scope.price,
                    }, function () {
                        success()
                        $mdBottomSheet.hide()
                    })
                })
            }
        }
    }).then(function () {
        success()
    })
}