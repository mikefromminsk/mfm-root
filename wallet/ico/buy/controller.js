function openIcoBuy($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain

            hasToken(domain)
            hasBalance(wallet.quote_domain)
            $scope.balance = $rootScope.coins[wallet.quote_domain].balance
            $scope.price = $rootScope.coins[domain].price

            postContract(domain, contract.wallet, {
                address: "ico"
            }, function (response) {
                $scope.icoBalance = response.amount
                $scope.$apply()
            })

            $scope.calcAmount = function () {
                var coin = $rootScope.coins[domain]
                $scope.amount = $scope.round($scope.total / coin.price, 2)
                return $scope.amount
            }

            $scope.calcTotal = function () {
                var coin = $rootScope.coins[domain]
                $scope.total = $scope.round($scope.amount * coin.price, 2)
                return $scope.amount
            }

            $scope.ico_buy = function () {
                $scope.in_progress = true
                wallet.calcKey("usdt/wallet", function (key, hash, username) {
                    postContractWithGas(domain, contract.ico_buy, {
                        address: username,
                        key: key,
                        next_hash: hash,
                        amount: $scope.amount,
                    }, function () {
                        success()
                        showSuccessDialog("You bought " + $scope.formatAmount($scope.amount, domain))
                    }, function () {
                        $scope.in_progress = false
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}