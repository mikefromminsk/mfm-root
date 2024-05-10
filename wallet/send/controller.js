function openSendDialog(domain, to_address, amount, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            if ((to_address || "") != "") {
                $scope.to_address = to_address
                $scope.block_to_address = true
            }

            if ((amount || "") != "") {
                $scope.amount = amount
            }

            $scope.send = function () {
                if (!$scope.to_address || !$scope.amount) {
                    return
                }
                hasToken()
                postContractWithGas(domain, "api/token/send.php", function (key, next_hash) {
                    return {
                        from_address: wallet.address(),
                        to_address: $scope.to_address,
                        password: key,
                        next_hash: next_hash,
                        amount: $scope.amount,
                    }
                }, function () {
                    showSuccessDialog("Sent " + $scope.formatAmount($scope.amount, domain) + " success", success)
                }, function (message) {
                    if (message.indexOf("receiver doesn't exist") != -1) {
                        showInfoDialog("This user dosent exist but you can invite him", function () {
                            openInvite(domain, success)
                        })
                    }
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
                    if (amount > 1){
                        return amount - 1
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
    })
}