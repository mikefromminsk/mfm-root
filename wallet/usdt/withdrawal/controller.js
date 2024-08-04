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

            $scope.withdrawal = function () {
                // test withdrawal address
                if (!$scope.withdrawal_address.startsWith("T") || !$scope.withdrawal_address.length == 34) {
                    $scope.errorAddress = "Invalid address"
                    return
                }
                if (!$scope.amount) {
                    return
                }
                if ($scope.total <= 0){
                    $scope.errorAmount = "Amount too low"
                    return;
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
                $scope.amount = $scope.coin.balance
                $scope.calcTotal()
            }

            $scope.calcTotal = function () {
                $scope.total = Math.max(0, $scope.amount - $scope.provider.fee)
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