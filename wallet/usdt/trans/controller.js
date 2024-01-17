function openUsdtTransactions(success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/usdt/trans/index.html',
        controller: function ($scope) {
            addFormats($scope)

            post("/wallet/api/trans_usdt.php", {
                address: wallet.address(),
            }, function (response) {
                $scope.trans = $scope.groupByTimePeriod(response.trans)
            })
        }
    }).then(function () {
        if (success)
            success()
    })
}