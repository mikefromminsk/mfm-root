function openOptionsDialog(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        locals: {
            domain: domain,
        },
        controller: function ($scope, $mdBottomSheet, locals) {
            $scope.locals = locals

            post("/wallet/api/contracts", {
                domain: domain,
            }, function (response) {
                $scope.contracts = response.contracts
                $scope.$apply()
            })

            $scope.sendDialog = function (domain) {
                openSendDialog(domain, success)
            }

            $scope.giveaway = function () {
                wallet.auth(function (username) {
                    post("/" + $scope.contracts['d904b40c305d9eafb68583178dfec8e5'], {
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