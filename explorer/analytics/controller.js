function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    function loadProfile(domain){
        postContract("wallet", "api/profile.php", {
            domain: domain
        }, function (response) {
            $scope.profile = response
            $scope.$apply()
        })
    }

    $scope.domain = $scope.getUriParam("domain") || 'data'
    loadProfile($scope.domain)
}