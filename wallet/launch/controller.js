function openLaunchDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/launch/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain
            $scope.amount = 1000000
            if (DEBUG) {
                $scope.domain = "super"
            }
            $scope.launch = function () {
                hasBalance(wallet.gas_domain, function () {
                    postWithGas("/wallet/api/launch.php", {
                        domain: $scope.domain,
                        address: wallet.username,
                        next_hash: wallet.calcStartHash($scope.domain + "/wallet"),
                        amount: 1000000,
                    }, function () {
                        storage.pushToArray(storageKeys.domains, $scope.domain)
                        showSuccessDialog("Token " + $scope.formatTicker($scope.domain) + " launched", success)
                    })
                })
            }
            setFocus("launch_token_name")
        }
    }).then(function () {
        if (success)
            success()
    })
}