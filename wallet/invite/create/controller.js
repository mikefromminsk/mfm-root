function openInvite(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/invite/create/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.amount

            $scope.create = function () {
                if (!$scope.amount || $scope.amount <= 0) {
                    return
                }
                let invite_code = randomString(8)
                postContractWithGas(domain, "api/token/invite/create.php", function (key, next_hash) {
                    return {
                        key: key,
                        next_hash: next_hash,
                        amount: $scope.amount,
                        invite_next_hash: md5(invite_code),
                    }
                }, function () {
                    openInviteCopy(domain,
                        invite_code,
                        success)
                })
            }

            $scope.setMax = function () {
                if (domain == wallet.gas_domain){
                    $scope.amount = Math.max(0, $scope.coin.balance - 1)
                } else {
                    $scope.amount = $scope.coin.balance
                }
            }

            $scope.getTotal = function (amount) {
                if (domain == wallet.gas_domain){
                    if (amount > 2){
                        return amount - 2
                    } else {
                        return 0
                    }
                } else {
                    return amount
                }
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
    }).then(function () {
        if (success)
            success()
    })
}