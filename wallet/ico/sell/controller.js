function openIcoSell(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/sell/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain

            if (DEBUG) {
                $scope.amount = 100
                $scope.price = 3
            }

            postContract(domain, contract.wallet, {
                address: wallet.username
            }, function (response) {
                $scope.balance = response.amount
            })

            hasBalance(domain, function () {
                setFocus("first_input")
            })

            $scope.ico_sell = function () {
                hasBalance(wallet.quote_domain, function () {
                    wallet.calcKey(domain + "/wallet", function (key, hash, username) {
                        postContractWithGas(domain, contract.ico_sell, {
                            key: key,
                            next_hash: hash,
                            amount: $scope.amount,
                            price: $scope.price,
                        }, function () {
                            showSuccessDialog("You open for sale " + $scope.formatTicker(domain))
                        })
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}