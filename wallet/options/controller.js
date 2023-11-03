function openOptionsDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        locals: {
            domain: domain,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            post("/wallet/api/options", {
                domain: domain,
            }, function (response) {
                $scope.options = response.result
                $scope.$apply()
            })

            $scope.sendDialog = function (wallet_path) {
                openSendDialog(wallet_path, success)
            }

            $scope.giveaway = function () {
                wallet.auth(function (username) {
                    post('/wallet/api/mining.php', {
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