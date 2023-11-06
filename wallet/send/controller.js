function openSendDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/send/index.html',
        locals: {
            domain: domain,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals
            $scope.to_address = 'admin'
            $scope.amount = 2
            $scope.send = function () {
                wallet.send(locals.domain,
                    $scope.to_address,
                    $scope.amount,
                    function (result) {
                        $mdBottomSheet.hide()
                    })
            }
        }
    }).then(success)
}