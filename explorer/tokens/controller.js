function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.wallet = wallet

    $scope.search_domain = ''
    $scope.$watch('search_domain', function (newValue) {
        if (newValue == null) return
        post("/wallet/api/search.php", {
            search_text: (newValue || ""),
        }, function (response) {
            $scope.searchCoins = response.result
            $scope.$apply()
        })
    })

    $scope.selectDomain = function (coin) {
        $scope.selectedCoin = coin
        loadProfile(coin.domain)
    }

    function loadProfile(domain){
        post("/wallet/api/profile.php", {
            domain: domain
        }, function (response) {
            $scope.profile = response
            $scope.$apply()
        })
    }

    loadProfile('data')
}