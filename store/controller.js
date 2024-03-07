function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.wallet = wallet
    $scope.apps = {}
    $scope.selectedCoin

    if ($scope.getUriParam("domain")) {
        storage.setString(storageKeys.selectedCoin, $scope.getUriParam("domain"))
    }

    $scope.openSettings = function () {
        openSettings($scope.selectedCoin.domain, init)
    }

    $scope.openProfile = function (app) {
        if (app.console){
            openWeb(location.origin + "/" + app.domain + "/console?domain=" + $scope.selectedCoin.domain, init)
        } else {
            openProfile(app)
        }
    }

    $scope.openLogin = function () {
        openLogin(init)
    }

    $scope.logout = function () {
        wallet.logout()
        $scope.selectedCoin = null
    }

    function init() {
        search()
        updateCoins()
    }

    function search(newValue) {
        post("/store/api/apps.php", {
            search_text: (newValue || ""),
            domain: (storage.getString(storageKeys.selectedCoin) || ""),
        }, function (response) {
            $scope.apps = response.apps || {}
            $scope.categories = response.categories
            $scope.$apply()
        })
    }

    function updateCoins() {
        var domains = storage.getStringArray(storageKeys.domains)
        post("/wallet/api/list.php", {
            domains: domains.join(","),
            address: wallet.address(),
        }, function (response) {
            $scope.coins = []
            let selectedCoin = storage.getString(storageKeys.selectedCoin)
            for (let coin of response.result) {
                if (coin.owner == wallet.address()) {
                    $scope.coins.push(coin)
                    if (coin.domain == selectedCoin)
                        $scope.selectedCoin = coin
                    if (selectedCoin == '' && $scope.selectedCoin == null)
                        $scope.selectedCoin = coin
                }
            }
            $scope.$apply()
        })
    }

    $scope.selectCoin = function (coin) {
        $scope.selectedCoin = coin
        storage.setString(storageKeys.selectedCoin, coin.domain)
        init()
    }

    $scope.installApp = function (app) {
        postContractWithGas("store", "api/install.php", {
            domain: $scope.selectedCoin.domain,
            app_domain: app.domain,
        }, function () {
            init()
            showSuccess("Install success")
        })
    }

    init()
}