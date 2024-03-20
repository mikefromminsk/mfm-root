function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.domain = $scope.getUriParam("domain")
    $scope.employees = []

    function init() {
        postContract($scope.domain, "api/salary/employees.php", {},function (result) {
            $scope.employees = result.employees
            $scope.balance = result.balance
            $scope.$apply()
        })
    }

    $scope.openMining = function () {
        openMining($scope.domain)
    }

    $scope.openUpgradeEmployee = function (employee, manager, amount) {
        openUpgradeEmployee($scope.domain, employee, manager, amount, init)
    }

    $scope.approve = function (employee, manager) {
        postContractWithGas($scope.domain, "api/salary/approve.php", {
            employee_address: employee,
            manager_address: manager,
        }, function () {
            showSuccessDialog("Approved " + employee + " success", init)
        })
    }

    $scope.send = function () {
        openSendDialog($scope.domain, "salary", 100000, init)
    }

    init()
}