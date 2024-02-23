function openSendDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            if (DEBUG) {
                $scope.to_address = 'user'
                $scope.amount = 2
            }
            $scope.send = function () {
                hasToken()
                postContractWithGas(domain, brc1.send, function (key, next_hash) {
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
        }
    })
}