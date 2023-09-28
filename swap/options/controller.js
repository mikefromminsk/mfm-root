function openOptionsDialog($mdBottomSheet, wallet_path, success) {
    $mdBottomSheet.show({
        templateUrl: '/swap/options/index.html',
        locals: {
            wallet_path: wallet_path,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals

            $scope.sendDialog = function (wallet_path) {
                openSendDialog($mdBottomSheet, wallet_path, success)
            }

            $scope.giveaway = function () {
                wallet.auth(function (username) {
                    post('/data/giveaway.php', {
                        address: username
                    }, function () {
                        success()
                        $mdBottomSheet.hide()
                    })
                })
            }
        }
    })
}