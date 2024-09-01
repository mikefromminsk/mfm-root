function openIcoSell($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/ico/sell/index.html",
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
                    postContractWithGas(domain, "ico/api/sell.php", function (key, hash) {
                        return {
                            key: key,
                            next_hash: hash,
                            amount: $scope.amount,
                            price: $scope.token.price || $scope.price,
                        }
                    }, function () {
                        showSuccessDialog("You open for sale " + $scope.formatTicker(domain), success)
                    })
                })
            }

            $scope.setMax = function () {
                $scope.amount = $scope.token.balance
                $scope.calcTotal()
            }

            $scope.calcTotal = function () {
                $scope.total = Math.max(0, $scope.round($scope.amount * $scope.token.price - $scope.gas_recommended, 2))
            }

            function init() {
                getProfile(domain, function (response) {
                    $scope.token = response
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