const brc1 = {
    send: '6ca3ad3fb30b51001944d4d90c2080d4',
    free_reg: '3d2a3f12adf303a58f8cb37c9ef51fa1',
    reg: 'b6aea855e7418054e0af65f2816452d0',
    drop: 'cd5ce018bfe962a3b8ae4117370edb04',
    init: '772df88baecd34099df80f0e592a9bc7',
    ico_buy: 'd670072f06bf06183fb422b9c28f1d8b',
    ico_sell: '8d0a5b6afe2082197857d58faef59655',
    bonus_create: 'f11bb3db4f36d360158b446c41b1bd6a',
    bonus_receive: '96eb30f335960041368dc63ee5e6ebec',
    wallet: '7242feda3f24473a3f86d9bd886e4510',
}

function openLogin(success) {
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
                        if (success)
                            success()
                        $scope.close()
                    })
                })
            }

        }
    })
}