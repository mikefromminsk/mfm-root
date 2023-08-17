function walletLogin($mdBottomSheet, app,  callback) {
    $mdBottomSheet.show({
        templateUrl: '/wallet/login.html',
        locals: {
            app: app,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            $scope.wallet = localStorage.getItem(locals.app)
            if ($scope.wallet != null) {
                $mdBottomSheet.hide($scope.wallet)
            } else {

            }
            $scope.wallet = {
                address: "user1",
                password: "password2",
            }
            $scope.login = function () {
                dataGet(locals.app + "/" + $scope.wallet.address, function (wallet) {
                    console.log(wallet)
                    if (md5($scope.wallet.password) == wallet.next_hash){
                        localStorage.setItem(locals.app, wallet)
                        $mdBottomSheet.hide(wallet)
                    }
                })
            }
        }
    }).then(callback)
}