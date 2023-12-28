function openAccount(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/account/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.version = storage.getString("version", "0.14")
            $scope.model = storage.getString("model", window.navigator.userAgent)
            $scope.wallet = wallet

            $scope.logout = function () {
                wallet.logout()
                storage.setString("onboarding_showed", "true")
            }
            $scope.login = function () {
                loginFunction(function () {
                    $scope.$apply()
                })
            }
            $scope.restart = function () {
                location.reload(true)
            }

            $scope.openPage = function () {
                openWeb("/wallet/docs/clear")
            }

        }
    }).then(function () {
        if (success)
            success()
    })
}