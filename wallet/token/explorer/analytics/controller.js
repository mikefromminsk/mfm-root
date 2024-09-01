function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    function loadProfile(domain){
        getProfile(domain, function (response) {
            $scope.profile = response
            $scope.$apply()
        })
    }

    $scope.domain = getParam("domain") || 'data'
    loadProfile($scope.domain)
}