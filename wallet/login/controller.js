function openLogin(success) {
    if (wallet.address() != "") {
        if (success) success()
        return
    }
    window.$mdDialog.show({
        templateUrl: '/wallet/login/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.username = ""
            if (DEBUG) {
                $scope.username = "admin"
                $scope.password = "pass"
            }

            // TODO validation
            $scope.login = function () {
                $scope.in_progress = true
                postContract(wallet.gas_domain, "api/token/wallet.php", {
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
                    postContract(wallet.gas_domain, "api/token/free_reg.php", {
                        address: $scope.username,
                        next_hash: md5(wallet.calcHash(wallet.gas_domain, $scope.username, $scope.password))
                    }, function () {
                        postContract(wallet.quote_domain, "api/token/free_reg.php", {
                            address: $scope.username,
                            next_hash: md5(wallet.calcHash(wallet.quote_domain, $scope.username, $scope.password))
                        }, function () {
                            setPin()
                        })
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
                openPin(null, function (pin) {
                    storage.pushToArray(storageKeys.domains, wallet.gas_domain)
                    storage.setString(storageKeys.username, $scope.username)
                    storage.setString(storageKeys.passhash, encode($scope.password, pin))
                    if (pin != null)
                        storage.setString(storageKeys.hasPin, true)

                    post("/wallet/api/settings/read.php", {
                        key: "domains",
                        user: $scope.username,
                    }, function (response) {
                        for (let setting of response.settings)
                            storage.pushToArray(storageKeys.domains, setting)
                        if (success) success()
                        $scope.close()
                    })
                })
            }

            $scope.changePass = function () {
                postContractWithGas("wallet", "api/change_pass.php", {
                    domain: 'data',
                    address: 'admin',
                    password: 'pass',
                })
            }
        }
    })
}