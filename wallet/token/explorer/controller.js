function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.params = window.location.search

    $scope.domain = getParam("domain") || 'data'

    if (getParam("txid")) {
        $scope.selectedIndex = 1
    }
}