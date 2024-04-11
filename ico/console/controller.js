function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.domain = $scope.getUriParam("domain")

    $scope.send = function () {
        openSendDialog($scope.domain, $scope.domain + "_ico", 200000)
    }

    function init() {
        post("/wallet/api/profile.php", {
            domain: $scope.domain,
            address: wallet.address(),
        }, function (response) {
            $scope.coin = response
            $scope.$apply()
        })
    }

    init()
}