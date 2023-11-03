function openSendDialog(wallet_path, success) {
    wallet.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        locals: {
            wallet_path: wallet_path,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            $scope.to_address = 'admin'
            $scope.amount = 2
            $scope.send = function () {
                wallet.send(locals.wallet_path,
                    $scope.to_address,
                    $scope.amount,
                    function (result) {
                        $mdBottomSheet.hide()
                    })
            }
        }
    }).then(success)
}