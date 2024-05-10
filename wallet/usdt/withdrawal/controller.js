function openWithdrawal(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/usdt/withdrawal/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.withdrawal_address = ""
            $scope.amount = ""

            if (DEBUG){
                $scope.withdrawal_address = "TCS4FD9XJ4abux72qy21Dc4DC7XWAHjvje"
                $scope.amount = 0.1
            }

            $scope.$watch('search_text', function (newValue) {
                if (newValue == null) return
                $scope.errorWithdrawalAddress = false
            })

            $scope.withdrawal = function () {
                // test withdrawal address
                if (!$scope.withdrawal_address.startsWith("T") || !$scope.withdrawal_address.length == 34) {
                    $scope.errorWithdrawalAddress = true
                    return
                }
                if (!$scope.amount) {
                    return
                }
                postContractWithGas("usdt", "api/withdrawal/start.php", function (key, nexthash) {
                    return {
                        address: wallet.address(),
                        key: key,
                        next_hash: nexthash,
                        withdrawal_address: $scope.withdrawal_address,
                        amount: $scope.amount,
                        provider: "TRON",
                    }
                }, function (response) {
                    showSuccessDialog("Your withdrawal in progress", success)
                })
            }

            $scope.setMax = function () {
                $scope.amount = Math.max(0, $scope.coin.balance - $scope.provider.fee)
            }

            $scope.getTotal = function () {
                return Math.max(0, $scope.amount - $scope.provider.fee)
            }

            function init() {
                postContract("wallet", "api/profile.php", {
                    domain: "usdt",
                    address: wallet.address(),
                }, function (response) {
                    $scope.coin = response
                    $scope.$apply()
                })
                postContract(wallet.quote_domain, "api/providers.php", {
                }, function (response) {
                    $scope.providers = response
                    $scope.provider = response["TRON"]
                    $scope.$apply()
                })
            }

            init()
        }

    }).then(function () {
        if (success)
            success()
    })
}