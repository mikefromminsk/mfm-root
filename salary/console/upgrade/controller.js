function openUpgradeEmployee(domain, employee_address, manager_address, amount, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/salary/console/upgrade/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain

            if ((employee_address || "") != "") {
                $scope.employee_address = employee_address
                $scope.block_employee_address = true
            }

            if ((manager_address || "") != "") {
                $scope.manager_address = manager_address
                $scope.block_manager_address = true
            } else {
                $scope.manager_address = wallet.address()
            }

            if ((amount || "") != "") {
                $scope.amount = amount
            }

            $scope.upgrade = function () {
                postContractWithGas(domain, "api/salary/upgrade.php", {
                    employee_address: $scope.employee_address,
                    manager_address: $scope.manager_address,
                    block_distance: DEBUG ? (7 * 24 * 60 * 60) : 10,
                    amount: $scope.amount,
                }, function () {
                    showSuccessDialog("Upgraded " + $scope.employee_address + " success", success)
                })
            }
        }
    })
}