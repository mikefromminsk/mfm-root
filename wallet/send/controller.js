function openSendDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        controller: function ($scope, $mdBottomSheet) {
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
                        $mdBottomSheet.hide()
                    })
            }
        }
    }).then(success)
}