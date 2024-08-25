function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    // Store
    addTokens($scope)

    // Store
    addStore($scope)

    //transactions
    addTransactions($scope)

    if (getParam("bonus") != null) {
        openShareReceive(getParam("bonus"))
    }

}