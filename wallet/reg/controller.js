function openRegDialog($mdBottomSheet, $mdToast, success) {
    $mdBottomSheet.show({
        templateUrl: '/wallet/reg/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.address = 'test1'
            $scope.password = 'password'
            $scope.reg = function () {
                wallet.reg($scope.address, $scope.password,
                    function () {
                        $mdBottomSheet.hide()
                    }, function () {
                        $mdToast.show($mdToast.simple()
                            .textContent('login or username is invalid'))
                    })
            }
        }
    }).then(function () {
        success()
    })
}