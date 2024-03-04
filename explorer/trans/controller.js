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
            if (getUriParam("domain")) {
                $scope.selectDomain($scope.searchCoins[0])
                openTran($scope.selectedCoin.domain, getUriParam("txid"))
            }
        })
    })

    if (getUriParam("domain")) {
        $scope.search_domain = getUriParam("domain")
    }

    function getUriParam(paramName){
        var uri = window.location.search.substring(1)
        var params = new URLSearchParams(uri)
        return params.get(paramName)
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