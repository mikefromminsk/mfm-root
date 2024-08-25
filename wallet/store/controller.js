function addStore($scope) {

    $scope.openAppSettings = function () {
        openAppSettings($scope.selectedCoin.domain, init)
    }

    $scope.openProfile = function (app) {
        if (app.installed) {
            openWeb(location.origin + "/" + app.domain + "?domain=" + $scope.selectedCoin.domain, init)
        } else {
            openProfile(app)
        }
    }

    $scope.selectCoin = function (coin) {
        $scope.selectedCoin = coin
        storage.setString(storageKeys.selectedCoin, coin.domain)
        $scope.selectTab(1)
        searchApp()
    }

    $scope.installApp = function (app) {
        postContractWithGas("wallet", "store/api/install.php", {
            domain: $scope.selectedCoin.domain,
            app_domain: app.domain,
        }, function () {
            showSuccess("Install success")
        })
    }

    $scope.selectTab = function (tab) {
        $scope.selectedIndex = tab
        if (tab == 2) {
            loadTrans()
        }
    }

    function searchApp(newValue) {
        postContract("wallet", "store/api/apps.php", {
            search_text: (newValue || ""),
            domain: (storage.getString(storageKeys.selectedCoin) || ""),
        }, function (response) {
            $scope.apps = response.apps || {}
            $scope.$apply()
        })
    }

    function init(){

    }
}