function openLoginDialog($mdBottomSheet, $mdToast, success) {
    $mdBottomSheet.show({
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope, $mdBottomSheet) {
            if (DEBUG) {
                $scope.address = "test1"
                $scope.password = "password"
            }
            $scope.mode = "login"
            $scope.login = function () {
                if ($scope.mode == "login") {
                    wallet.login($scope.address, $scope.password,
                        function () {
                            $mdBottomSheet.hide()
                        }, function () {
                            $mdToast.show($mdToast.simple()
                                .textContent('login or username is invalid'))
                            if (confirm('Do you want to create an account?')) {
                                $scope.mode = "registration"
                                $scope.login()
                            }
                        })
                } else if ($scope.mode == "registration") {
                    wallet.reg($scope.address, $scope.password,
                        function () {
                            $mdBottomSheet.hide()
                        }, function () {
                            $mdToast.show($mdToast.simple()
                                .textContent('login or username is invalid'))
                        })
                }
            }
        }
    }).then(function () {
        success();
    })
}