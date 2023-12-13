function openAccount(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/account/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.version = storage.getString("version", "0.14")
            $scope.model = storage.getString("model", window.navigator.userAgent)
            $scope.wallet = wallet

            $scope.back = function () {
                $mdBottomSheet.hide()
            }
            $scope.logout = function () {
                wallet.logout()
            }
            $scope.login = function () {
                loginFunction(function () {
                    $scope.$apply()
                })
            }
            $scope.restart = function () {
                location.reload(true)
            }

            $scope.openMessages = function () {
                openMessages("admin")
            }

            $scope.openPage = function () {
                openWeb("/wallet/clear")
            }

        }
    }).then(function () {
        if (success)
            success()
    })
}