function openIcoBuy($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain

            postContract(domain, contract.wallet, {
                address: "ico"
            }, function (response) {
                $scope.icoBalance = response.amount
                $scope.$apply()
            })

            $scope.base = {};
            $scope.quote = {};
            post("/wallet/api/list.php", {
                domains: domain + "," + wallet.quote_domain,
                address: wallet.username,
            }, function (response) {
                if (response.result[0].domain == domain){
                    $scope.base = response.result[0]
                    $scope.quote = response.result[1]
                } else {
                    $scope.base = response.result[1]
                    $scope.quote = response.result[0]
                }
                $scope.balance = $scope.quote.balance

                $scope.balance = $scope.quote.balance
                $scope.price = $scope.base.price
                $scope.$apply()
            })

            $scope.calcAmount = function () {
                $scope.amount = $scope.round($scope.total / $scope.base.price, 2)
                return $scope.amount
            }

            $scope.calcTotal = function () {
                $scope.total = $scope.round($scope.amount * $scope.base.price, 2)
                return $scope.amount
            }

            $scope.ico_buy = function () {
                hasToken(domain, function () {
                    hasBalance(wallet.quote_domain, function () {
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
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}