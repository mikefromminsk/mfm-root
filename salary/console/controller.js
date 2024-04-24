function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    var domain = $scope.getUriParam("domain")
    $scope.domain = domain

    $scope.employees = []

    function init() {
        loadProfile()
        loadEmployees()
    }

    function loadProfile() {
        postContract("wallet", "api/profile.php", {
            domain: domain,
            address: "salary",
        }, function (response) {
            $scope.coin = response
            $scope.$apply()
        })
    }

    function loadEmployees() {
        postContract($scope.domain, "api/salary/employees.php", {},function (result) {
            $scope.employees = result.employees
            $scope.$apply()
        })
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

    $scope.openSettings = function () {
        openSettings($scope.domain, init)
    }
    init()
}