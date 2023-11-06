function openLaunchDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/launch/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.domain = locals.domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }
            $scope.launch = function () {
                let path = $scope.domain + "/wallet"
                wallet.calckey(path, function (key, next_hash, username, password) {
                    postWithGas("/wallet/api/launch", {
                        path: path,
                        address: username,
                        next_hash: next_hash,
                        amount: $scope.amount,
                    }, function () {
                        wallet.domainAdd($scope.path)
                        window.showSuccess("token launched", success)
                        $mdBottomSheet.hide()
                    })
                })
            }
        }
    }).then(function () {
        success()
    })
}