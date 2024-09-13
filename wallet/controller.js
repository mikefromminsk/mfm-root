function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    addTokens($scope)

    if (getParam("bonus") != null) {
        openShareReceive(getParam("bonus"))
    }

    $scope.selectTab = function (tab) {
        $scope.selectedIndex = tab
        if (tab == 0) {
            //wallet
            addTokens($scope)
        } else if (tab == 1) {
            //store
            addStore($scope)
        } else if (tab == 2) {
            //transactions
            addTransactions($scope)
        }
    }

    connectWs();
}