function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.searchTxid = ""

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
                if ($scope.getUriParam("txid"))
                    openTran($scope.selectedCoin.domain, $scope.getUriParam("txid"))
            }
        })
    })

    if ($scope.getUriParam("domain")) {
        $scope.search_domain = $scope.getUriParam("domain")
    }

    $scope.selectDomain = function (coin) {
        $scope.selectedCoin = coin
        $scope.searchCoins = [coin]
        post("/wallet/api/trans_domain.php", {
            domain: coin.domain,
        }, function (response) {
            $scope.trans = $scope.groupByTimePeriod(response.trans)
            $scope.$apply()
        })
    }

    $scope.openTran = function (tran) {
        openTran(tran.domain, tran.txid)
    }

    $scope.searchTran = function () {
        openTran($scope.selectedCoin.domain, $scope.searchTxid)
    }
}