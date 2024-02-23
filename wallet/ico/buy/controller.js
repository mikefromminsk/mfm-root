function openIcoBuy($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain

            postContract(domain, brc1.wallet, {
                address: "ico"
            }, function (response) {
                $scope.icoBalance = response.amount
                $scope.$apply()
            })

            $scope.base = {};
            $scope.quote = {};
            post("/wallet/api/list.php", {
                domains: domain + "," + wallet.quote_domain,
                address: wallet.address(),
            }, function (response) {
                if (response.result[0].domain == domain) {
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
                            postContractWithGas(wallet.quote_domain, "", function (usdt_key, usdt_next_hash) {
                                postContractWithGas(domain, brc1.ico_buy, {
                                    address: wallet.address(),
                                    key: usdt_key,
                                    next_hash: usdt_next_hash,
                                    amount: $scope.amount,
                                }, function () {
                                    showSuccessDialog("You bought " + $scope.formatAmount($scope.amount, domain), success)
                                }, function () {
                                    $scope.in_progress = false
                                })
                                return null
                            })

                        })
                    }
                )
            }
        }
    })
}