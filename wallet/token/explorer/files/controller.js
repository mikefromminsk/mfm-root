function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.path = []
    $scope.search_domain = ''
    $scope.subdir = ''
    $scope.$watch('search_domain', function (newValue) {
        if (newValue == null) return
        post("/wallet/api/search.php", {
            search_text: (newValue || ""),
        }, function (response) {
            $scope.searchCoins = response.result
            if (getParam("domain"))
                $scope.selectDomain($scope.searchCoins[0])
            $scope.$apply()
        })
    })
    if (getParam("domain")) {
        $scope.search_domain = getParam("domain")
    }

    $scope.selectDomain = function (coin) {
        $scope.selectedCoin = coin
        $scope.path = [coin.domain]
        loadDir()
    }

    function loadDir() {
        post("/explorer/api/info_dir.php", {
            path: $scope.path.join("/")
        }, function (response) {
            $scope.info = response
            $scope.$apply()
        })
    }

    $scope.addPath = function (key) {
        $scope.path.push(key)
        loadDir(key)
    }

    $scope.backPath = function () {
        $scope.path.pop()
        loadDir()
    }

    $scope.toImg = function (data_type) {
        if (data_type == -1) {
            return "folder.svg"
        } else {
            return "file.svg"
        }
    }

    $scope.getPath = function () {
        return $scope.path.join("/")
    }
}