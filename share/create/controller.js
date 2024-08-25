function openShare(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/share/create/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.amount

            $scope.create = function () {
                if (!$scope.amount || $scope.amount <= 0) {
                    return
                }
                let invite_code = randomString(8)
                postContractWithGas(domain, "api/share/share.php", function (pass) {
                    return {
                        pass: pass,
                        amount: $scope.amount,
                        invite_next_hash: md5(invite_code),
                    }
                }, function () {
                    openInviteCopy(domain,
                        invite_code,
                        success)
                })
            }

            $scope.getMax = function () {
                if (domain == wallet.gas_domain){
                    return Math.round(Math.max(0, $scope.coin.balance - $scope.gas_recommended), 2)
                } else {
                    return $scope.coin.balance
                }
            }

            $scope.setMax = function () {
                $scope.amount = $scope.getMax()
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
                /*postContract(domain, "api/token/invite/create.php", {
                    gas_spent: 1,
                }, function (response) {
                    $scope.gas_recommended = response.gas_recommended
                    $scope.$apply()
                })*/
            }

            init()
        }
    }).then(function () {
        if (success)
            success()
    })
}