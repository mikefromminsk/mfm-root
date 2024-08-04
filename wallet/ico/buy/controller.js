function openIcoBuy($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain

            $scope.ico_buy = function () {
                if (!$scope.total) {
                    return
                }
                hasToken(domain, function () {
                        hasBalance(wallet.quote_domain, function () {
                            $scope.in_progress = true
                            postContractWithGas(domain, "api/token/ico/buy.php", {
                                amount: $scope.amount,
                            }, function () {
                                showSuccessDialog("You bought " + $scope.formatAmount($scope.amount, domain), success)
                            }, function () {
                                $scope.in_progress = false
                            })
                        })
                    }
                )
            }

            $scope.setMax = function () {
                $scope.total = $scope.coin.gas_balance
                $scope.calcAmount()
            }

            $scope.calcAmount = function () {
                $scope.amount = Math.max(0, $scope.round(($scope.total - $scope.gas_recommended) / $scope.coin.price, 2))
            }

            function init() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.coin = response
                    $scope.$apply()
                })
                postContract(domain, "api/token/ico/sell.php", {
                    gas_spent: 1,
                }, function (response) {
                    $scope.gas_recommended = response.gas_recommended
                    $scope.$apply()
                })
            }

            init()
        }
    })
}