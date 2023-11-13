function openLaunchDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/launch/index.html",
        locals: {
            domain: domain
        },
        controller: function ($scope, $mdBottomSheet, $http, locals) {
            $scope.domain = locals.domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }
            $scope.launch = function () {
                postWithGas("/wallet/api/launch.php", {
                    domain: $scope.domain
                }, function () {
                    postContractWithGas($scope.domain, data10.init, {
                        address: wallet.username,
                        next_hash: md5(wallet.calchashStart($scope.domain + "/wallet")),
                    }, function () {
                        wallet.domainAdd($scope.domain)
                        $mdBottomSheet.hide()
                        showSuccessDialog("Token " + $scope.domain + " launched")
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}