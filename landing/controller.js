function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.tokenCategories = tokenCategories

    $scope.openAccount = function () {
        openAccount()
    }

    post("api/info.php", {}, function (response) {
        document.title = response.info.domain.toUpperCase()
        $scope.info = response.info
        $scope.$apply()
    })
}