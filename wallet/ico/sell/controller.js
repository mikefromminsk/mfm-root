function openIcoSell($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/sell/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain

            hasBalance(domain, function () {
                setFocus("first_input")
            })

            $scope.ico_sell = function () {
                if (!$scope.amount) {
                    return
                }
                hasToken(wallet.quote_domain, function () {
                    postContractWithGas(domain, "api/token/ico/sell.php", function (key, hash) {
                        return {
                            key: key,
                            next_hash: hash,
                            amount: $scope.amount,
                            price: $scope.coin.price || $scope.price,
                        }
                    }, function () {
                        showSuccessDialog("You open for sale " + $scope.formatTicker(domain), success)
                    })
                })
            }

            $scope.calcTotal = function () {
                if (domain == wallet.gas_domain) {
                    $scope.total = Math.max(0, $scope.round($scope.amount * $scope.coin.price, 2) - 1)
                } else {
                    $scope.total = $scope.round($scope.amount * $scope.coin.price, 2)
                }
                return $scope.total
            }

            $scope.setMax = function () {
                $scope.amount = Math.max(0, $scope.coin.balance - 1)
                $scope.calcTotal()
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