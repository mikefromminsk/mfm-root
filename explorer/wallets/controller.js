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
            if ($scope.getUriParam("domain")) {
                $scope.selectDomain($scope.searchCoins[0])
            }
        })
    })

    if ($scope.getUriParam("domain")) {
        $scope.search_domain = $scope.getUriParam("domain")
    }

    $scope.selectDomain = function (coin) {
        $scope.selectedCoin = coin
        searchDomain($scope.selectedCoin.domain, "")
    }

    $scope.search_user = ''
    $scope.$watch('search_user', function (newValue) {
        if (newValue == null) return
        searchDomain($scope.selectedCoin.domain, newValue)
    })

    function searchDomain(domain, search){
        post("/data/api/search.php", {
            path: domain + "/wallet",
            search_text: (search || ""),
        }, function (response) {
            $scope.users = response.result
            $scope.$apply()
        })
    }

    $scope.selectUser = function (user) {
        updateCoins(user)
    }

    function updateCoins(user) {
        post("/wallet/api/settings/read.php", {
            key: "domains",
            user: user,
        }, function (response) {
            var domains = response.settings
            if (domains.length > 0) {
                post("/wallet/api/list.php", {
                    domains: domains.join(","),
                    address: user,
                }, function (response) {
                    $scope.activeCoins = response.result
                    $scope.filteredActiveCoins = response.result
                    $scope.$apply()
                })
            } else {
                $scope.activeCoins = []
                $scope.filteredActiveCoins = []
            }
        })
    }
}