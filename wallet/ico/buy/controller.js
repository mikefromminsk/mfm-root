function openIcoBuy($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain

            postContract(domain, "api/token/wallet.php", {
                address: "ico"
            }, function (response) {
                $scope.icoBalance = response.amount
                $scope.$apply()
            })

            $scope.calcAmount = function () {
                if (domain == wallet.gas_domain) {
                    $scope.amount = Math.max(0, $scope.round($scope.total / $scope.coin.price, 2) - 1)
                } else {
                    $scope.amount = $scope.round($scope.total / $scope.coin.price, 2)
                }
                return $scope.amount
            }

            $scope.calcTotal = function () {
                $scope.total = $scope.round($scope.amount * $scope.coin.price, 2)
                return $scope.amount - 1
            }

            $scope.ico_buy = function () {
                if (!$scope.total) {
                    return
                }
                hasToken(domain, function () {
                        hasBalance(wallet.quote_domain, function () {
                            $scope.in_progress = true
                            postContractWithGas(wallet.quote_domain, "", function (usdt_key, usdt_next_hash) {
                                postContractWithGas(domain, "api/token/ico/buy.php", {
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

            $scope.setMax = function () {
                $scope.total = $scope.coin.balance
            }

            function init() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.coin = response
                    $scope.$apply()
                })
            }

            init()
        }
    })
}