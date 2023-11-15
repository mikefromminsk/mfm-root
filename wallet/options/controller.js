function openOptionsDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain
            $scope.data10 = contract

            postContract(domain, contract.wallet, {
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
                    postContract(domain, contract.drop, {
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