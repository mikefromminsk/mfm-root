function walletSend($mdBottomSheet, app, coin, wallet, callback) {
    $mdBottomSheet.show({
        templateUrl: '/wallet/send.html',
        locals: {
            app: app,
            coin: coin,
            wallet: wallet,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            $scope.toAddress = "admin"
            $scope.amount = 100
            $scope.send = function () {
                dataSend(locals.app,
                    locals.wallet.address,
                    $scope.toAddress,
                    walletPassword(locals.app + "/" + wallet),
                    walletNextHash(locals.app + "/" + wallet),
                    $scope.amount,
                    function (result) {

                    })
            }
        }
    }).then(callback)
}