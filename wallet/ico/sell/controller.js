function openIcoSell($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/sell/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            if (DEBUG) {
                $scope.price = 3
            }
            dataGet("wallet/info/" + domain + "/total", function (total) {
                $scope.total = total
                $scope.setPortion($scope.selectedPortion)
                $scope.$apply()
            })
            $scope.total = 0
            $scope.amount = 0
            $scope.portions = [1, 5, 10, 15, 25]
            $scope.selectedPortion = $scope.portions[2]
            $scope.setPortion = function (item) {
                $scope.selectedPortion = item
                $scope.amount = $scope.total / $scope.selectedPortion
            }

            $scope.balance = $rootScope.coins[domain].balance

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