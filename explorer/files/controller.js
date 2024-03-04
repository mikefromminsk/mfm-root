function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.search_user = ''
    $scope.$watch('search_user', function (newValue) {
        $scope.files = []
        if (newValue == null) return
        post("/data/api/search.php", {
            path: "data/wallet",
            search_text: (newValue || ""),
        }, function (response) {
            $scope.users = response.result
            $scope.$apply()
        })
    })

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