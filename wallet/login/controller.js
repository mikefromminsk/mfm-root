function loginFunction(success) {
    window.$mdDialog.show({
        clickOutsideToClose: true,
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope, $mdDialog) {
            $scope.address = getString("email")
            if (DEBUG) {
                if ($scope.address == "")
                    $scope.address = "user"
                $scope.password = "pass"
            }
            $scope.selectEmail = function () {
                if (window.NativeAndroid != null) {
                    window.NativeAndroid.selectEmail()
                }
            }
            $scope.mode = "login"
            $scope.login = function () {
                if ($scope.mode == "login") {
                    wallet.login($scope.address, $scope.password,
                        function () {
                            $mdDialog.hide()
                        }, function () {
                            showError('login or username is invalid')
                            if (confirm('Do you want to create an account?')) {
                                $scope.mode = "registration"
                                $scope.login()
                            }
                        })
                } else if ($scope.mode == "registration") {
                    wallet.reg($scope.address, $scope.password,
                        function () {
                            $mdDialog.hide()
                        }, function () {
                            showError('login or username is invalid')
                        })
                }
            }
        }
    }).then(function () {
        wallet.domainAdd(wallet.GAS_NAME)
        success()
    })
}