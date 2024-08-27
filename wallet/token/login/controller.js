function openLogin(success) {
    if (wallet.address() != "") {
        if (success) success()
        return
    }
    window.$mdDialog.show({
        templateUrl: '/wallet/token/login/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.username = ""
            if (DEBUG) {
                $scope.username = "admin"
                $scope.password = "pass"
            }

            // TODO validation
            $scope.login = function () {
                if (!$scope.username || !$scope.password) {
                    return
                }
                $scope.in_progress = true
                postContract("token", "address.php", {
                    domain: wallet.gas_domain,
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
                    postContract("token", "send.php", {
                        domain: wallet.gas_domain,
                        from_address: "owner",
                        to_address: $scope.username,
                        amount: 0,
                        pass: ":" + md5(wallet.calcHash(wallet.gas_domain, $scope.username, $scope.password))
                    }, function () {
                        setPin()
                    })
                })
            }

            $scope.$watch(function () {
                return $scope.username
            }, function (newValue, oldValue) {
                if (newValue != newValue.toLowerCase())
                    $scope.username = oldValue
                if (newValue.match(new RegExp("\\W")))
                    $scope.username = oldValue
            })

            function setPin() {
                getPin(function (pin) {
                    storage.setString(storageKeys.username, $scope.username)
                    storage.setString(storageKeys.passhash, encode($scope.password, pin))
                    if (pin != null)
                        storage.setString(storageKeys.hasPin, true)
                    if (success) success()
                    $scope.close()
                })
            }
        }
    })
}