function openIcoBuy(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico_buy/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.domain = locals.domain
            if (DEBUG) {
                $scope.amount = 1000000
            }
            $scope.ico_buy = function () {
                wallet.auth(function (username) {
                    postContract(domain, data10.ico_buy, {
                        address: username,
                        key: null,
                        next_hash: wallet.calchashStart(domain + "/wallet/ico"),
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