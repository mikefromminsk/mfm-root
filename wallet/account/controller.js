function openAccount(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/account/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.logged_in = wallet.isLoggedIn()
            $scope.title = wallet.isLoggedIn() ? wallet.username : "Settings"

            $scope.back = function () {
                $mdBottomSheet.hide()
            }
            $scope.logout = function () {
                wallet.logout()
                showSuccess("Success logout", function () {
                    $mdBottomSheet.hide()
                })
            }
            $scope.login = function () {
                loginFunction(function () {
                    $mdBottomSheet.hide()
                })
            }
            $scope.restart = function () {
                location.reload(true)
            }
        }
    }).then(success)
}