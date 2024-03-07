function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.domain = $scope.getUriParam("domain")

    $scope.openMining = function () {
        openMining($scope.domain)
    }

    $scope.init = function () {
        postContractWithGas($scope.domain, "api/mining/init.php", {}, function (response) {
            showSuccessDialog("Success " + response.success, init)
        })
    }

    $scope.reset = function () {
        postContractWithGas($scope.domain, "api/mining/reset.php", {}, function (response) {
            showSuccessDialog("Success " + response.success)
        })
    }

    $scope.send = function () {
        openSendDialog($scope.domain, "mining", 200000)
    }

    function init(){
        dataGet($scope.domain + "/wallet/mining/script", function (result) {
            $scope.not_initialized = result == null
            $scope.$apply()
        })
    }
    init()
}