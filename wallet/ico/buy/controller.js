function openIcoBuy(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/ico/buy/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            if (DEBUG) {
                $scope.amount = 1
            }

            postContract(wallet.quote_domain, contract.wallet, {
                address: wallet.username
            }, function (response) {
                $scope.balance = response.amount
            })

            dataGet(domain + "/price", function (response) {
                if (response == null){
                    showInfoDialog("Token " + domain + " is not for sale")
                } else {
                    postContract(domain, contract.wallet, {
                        address: "ico"
                    }, function (response) {
                        $scope.max = response.amount
                        $scope.$apply()
                    })
                    $scope.price = response
                    $scope.$apply()
                    setFocus("main_button")
                }
            })


            $scope.ico_buy = function () {
                wallet.calcKey("usdt/wallet", function (key, hash, username) {
                    postContractWithGas(domain, contract.ico_buy, {
                        address: username,
                        key: key,
                        next_hash: hash,
                        amount: $scope.amount,
                    }, function () {
                        success()
                        showSuccessDialog("You bought " + $scope.formatAmount($scope.amount, domain))
                    })
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}