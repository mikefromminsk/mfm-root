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
                storage.setString(storageKeys.onboardingShowed, "true")
            }
            $scope.login = function () {
                $scope.back()
                openLogin(success)
            }
            $scope.restart = function () {
                location.reload(true)
            }

            $scope.openPage = function () {
                openWeb(location.origin + "/wallet/docs/clear/index.html")
            }

        }
    }).then(function () {
        if (success)
            success()
    })
}