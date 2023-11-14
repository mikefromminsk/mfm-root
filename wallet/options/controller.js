function openOptionsDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.balance = 0
            $scope.data10 = data10

            postContract(domain, data10.wallet, {
                address: wallet.username,
            }, function (response) {
                $scope.balance = response.amount
                $scope.$apply()
            })

            post("/wallet/api/contracts.php", {
                domain: domain,
            }, function (response) {
                $scope.contracts = response.contracts
                $scope.$apply()
            })

            $scope.sendDialog = function () {
                openSendDialog(domain, success)
            }

            $scope.giveaway = function () {
                wallet.auth(function (username) {
                    postContract(domain, data10.drop, {
                        address: username
                    }, function () {
                        success()
                        $mdBottomSheet.hide()
                    })
                })
            }

            $scope.ico_sell = function () {
                openIcoSell(domain, success)
            }

            $scope.ico_buy = function () {
                openIcoBuy(domain, success)
            }

            $scope.share = function () {
                openInvite(domain, success)
            }
        }
    })
}