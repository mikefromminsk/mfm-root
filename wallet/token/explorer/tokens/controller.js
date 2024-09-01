function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

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

    function loadProfile(domain) {
        getProfile(domain, function (response) {
            $scope.profile = response
            $scope.$apply()
        })
    }

    if (getParam("domain")) {
        $scope.search_domain = getParam("domain")
    } else {
        loadProfile('data')
    }
}