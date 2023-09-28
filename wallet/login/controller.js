function openLoginDialog($mdBottomSheet, $mdToast, success) {
    $mdBottomSheet.show({
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.address = 'test1'
            $scope.password = 'password'
            $scope.login = function () {
                wallet.login($scope.address, $scope.password,
                    function () {
                        $mdBottomSheet.hide()
                    }, function () {
                        $mdToast.show($mdToast.simple()
                            .textContent('login or username is invalid'))
                    })
            }
        }
    }).then(function () {
        success();
    })
}