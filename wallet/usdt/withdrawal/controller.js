function openWithdrawal(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/usdt/withdrawal/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.withdrawal_address = ""
            $scope.amount = ""

            $scope.withdrawal = function () {
                postContractWithGas("usdt", "api/withdrawal_start.php", function (key, nexthash) {
                    return {
                        address: wallet.address(),
                        key: key,
                        nexthash: nexthash,
                        withdrawal_address: $scope.withdrawal_address,
                        amount: $scope.amount,
                        chain: "TRON",
                        withdrawal_id: $scope.random(10000, 99999),
                    }
                }, function (response) {
                    showSuccessDialog("Your withdrawal in progress", success)
                })
            }
        }

    }).then(function () {
        if (success)
            success()
    })
}