function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.apps = {}
    $scope.selectedCoin
    $scope.domain = "data"

    $scope.openGenerator = function () {
        openGenerator($scope.selectedCoin.domain, init)
    }

    $scope.openProfile = function (app) {
        openProfile(app)
    }

    $scope.openLogin = function () {
        openLogin(init)
    }

    $scope.logout = function () {
        wallet.logout()
        $scope.selectedCoin = null
    }

    function init() {
        updateCoins()
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
            loadCollections()
        })
    }

    function loadCollections() {
        post("/nft/api/collections.php", {
            domain: $scope.selectedCoin.domain,
        }, function (response) {
            $scope.collections = response
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
            showSuccess("Install success")
        })
    }

    init()
}