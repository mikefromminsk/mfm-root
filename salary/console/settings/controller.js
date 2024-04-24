function openSettings(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/salary/console/settings/index.html',
        controller: function ($scope) {
            addFormats($scope)
            window.$mdToast = $mdToast
            window.$mdBottomSheet = $mdBottomSheet
            window.$mdDialog = $mdDialog

            $scope.domain = domain
            $scope.balances = {}

            function getBalance(domain, address) {
                dataGet(domain + "/wallet/" + address + "/amount", function (amount) {
                    $scope.balances[address] = {
                        amount: amount,
                        address: address,
                        domain: domain,
                    }
                    $scope.$apply()
                })
            }

            $scope.initilize = function () {
                postContractWithGas(domain, "api/salary/init.php", {}, function () {
                    showSuccess("Initilized", init)
                })
            }

            function init() {
                getBalance(domain, "salary")
            }

            $scope.send = function (domain, address, amount) {
                openSendDialog(domain, address, amount, init)
            }

            init()

        }
    }).then(function () {
        if (success)
            success()
    })
}