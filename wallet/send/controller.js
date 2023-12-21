function openSendDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.domain = domain
            if (DEBUG) {
                $scope.to_address = 'user'
                $scope.amount = 2
            }
            $scope.send = function () {
                wallet.send(domain,
                    $scope.to_address,
                    $scope.amount,
                    function () {
                        postWithGas('/wallet/api/messages/send.php', {
                            to_address: $scope.to_address,
                            message: "You have been received " + $scope.formatAmount($scope.amount, domain),
                            token: storage.getString("fcm_token"),
                        }, function () {
                        })
                        showSuccessDialog("Sent " + $scope.formatAmount($scope.amount, domain) + " success", success)
                    })
            }
        }
    })
}