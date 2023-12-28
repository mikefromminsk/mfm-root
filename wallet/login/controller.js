function loginFunction(success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.username = ""
            if (DEBUG) {
                if ($scope.username == "")
                    $scope.username = "user"
                $scope.password = "pass"
            }
            setFocus("first_input")

            // TODO validation
            $scope.login = function () {
                $scope.in_progress = true
                postContract(wallet.gas_domain, contract.wallet, {
                    address: $scope.username,
                }, function (response) {
                    $scope.in_progress = false
                    if (md5(wallet.calcHash(wallet.gas_domain, $scope.username, $scope.password, response.prev_key)) == response.next_hash) {
                        setPin()
                    } else {
                        showError("password invalid")
                    }
                }, function () {
                    $scope.in_progress = false
                    postContract(wallet.gas_domain, contract.free_reg, {
                        address: $scope.username,
                        next_hash: md5(wallet.calcHash(wallet.gas_domain, $scope.username, $scope.password))
                    }, function () {
                        setPin()
                    })
                })
            }

            function setPin() {
                openPin(wallet.gas_domain, function (pin) {
                    storage.setString(storageKeys.username, $scope.username)
                    storage.setString(storageKeys.passhash, encode($scope.password, pin))
                    if (success)
                        success()
                    $scope.close()
                })
            }

        }
    })
}