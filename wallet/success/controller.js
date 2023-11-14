function showSuccessDialog(message, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/success/index.html',
        locals: {
            message: message
        },
        controller: function ($scope, locals, $mdBottomSheet) {
            $scope.locals = locals
            new Audio("/wallet/success/payment_success.mp3").play()
            $scope.close = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}