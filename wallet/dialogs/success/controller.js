function showSuccessDialog(message, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/dialogs/success/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.message = message
            new Audio("/wallet/dialogs/success/payment_success.mp3").play()
            $scope.close = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}