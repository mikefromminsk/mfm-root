function openIcoSell($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/sell/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.wallet = wallet
            $scope.domain = domain
            $scope.price = 1
            $scope.total = 0
            $scope.amount = 0
            $scope.portions = [1, 5, 10, 50, 100]
            $scope.selectedPortion = $scope.portions[2]
            $scope.setPortion = function (item) {
                $scope.selectedPortion = item
                $scope.amount = ($scope.base.balance / 100) * $scope.selectedPortion
            }
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
                if ($scope.base.price != 0)
                    $scope.hasPrice = true
                $scope.balance = $scope.quote.balance
                dataGet("wallet/info/" + domain + "/total", function (total) {
                    $scope.total = total
                    $scope.setPortion($scope.selectedPortion)
                    $scope.$apply()
                })
                $scope.$apply()
            })

            hasBalance(domain, function () {
                setFocus("first_input")
            })

            $scope.ico_sell = function () {
                hasToken(wallet.quote_domain, function () {
                    postContractWithGas(domain, "api/ico/sell.php", function (key, hash) {
                        return {
                            key: key,
                            next_hash: hash,
                            amount: $scope.amount,
                            price: $scope.price,
                        }
                    }, function () {
                        showSuccessDialog("You open for sale " + $scope.formatTicker(domain), success)
                    })
                })
            }
        }
    })
}