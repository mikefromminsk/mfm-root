function showInfoDialog(message, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/dialogs/info/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.message = message
            $scope.close = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}