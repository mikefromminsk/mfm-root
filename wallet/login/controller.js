function loginFunction(success) {
    window.$mdDialog.show({
        clickOutsideToClose: true,
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope, $mdDialog) {
            $scope.address = storage.getString("email")
            if (DEBUG) {
                if ($scope.address == "")
                    $scope.address = "user"
                $scope.password = "pass"
            }
            setFocus("first_input")
            $scope.login = function () {
                // TODO validation
                $scope.in_progress = true
                wallet.login($scope.address, $scope.password,
                    function () {
                        $mdDialog.hide($scope.address, $scope.password)
                    }, function () {
                        wallet.reg($scope.address, $scope.password,
                            function () {
                                $mdDialog.hide($scope.address, $scope.password)
                            }, function () {
                                $scope.in_progress = false
                                showError('login or username is invalid')
                            })
                    })
            }
        }
    }).then(function (username, password) {
        storage.pushToArray(storageKeys.domains, wallet.gas_domain)
        if (success)
            success(username, password)
    })
}