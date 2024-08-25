function openShareReceive(share_pass, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/share/receive/index.html",
        controller: function ($scope) {
            addFormats($scope)

            var bonus = share_pass.split(":")

            $scope.receive = function () {
                if (wallet.address() == "") {
                    openLogin($scope.receive)
                } else {
                    postContractWithGas(bonus[0], "api/share/receive.php", {
                        to_address: wallet.address(),
                        invite_key: bonus[1],
                    }, function (response) {
                        showSuccessDialog("You have been received " + $scope.formatAmount(response.received), init)
                    }, function () {
                        showInfoDialog("Bonus is invalid", init)
                    })
                }
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}