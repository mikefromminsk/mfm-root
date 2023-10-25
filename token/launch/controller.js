function openLaunchDialog($mdBottomSheet, $mdToast, success) {
    $mdBottomSheet.show({
        templateUrl: '/swap/launch/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.path = "dot"
            $scope.total = 1000000
            $scope.launch = function () {
                wallet.auth(function () {
                    wallet.postWithGas('/data/create.php', {
                        path: $scope.path + '/wallet',
                        address: wallet.username,
                        next_hash: wallet.calchash($scope.name, wallet.username, wallet.password),
                        amount: $scope.total,
                    }, function () {
                        $mdToast.show($mdToast.simple()
                            .textContent('launched'))
                        success()
                        $mdBottomSheet.hide()
                    })
                })
            }
        }
    })
}