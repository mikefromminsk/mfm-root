function openTransactions(domain) {
    window.$mdDialog.show({
        templateUrl: '/wallet/trans/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.username = wallet.address()
            $scope.in_progress = true

            post("/wallet/api/trans_user.php", {
                domain: domain,
                address: wallet.address(),
            }, function (response) {
                $scope.in_progress = false
                $scope.trans = $scope.groupByTimePeriod(response.trans)
                $scope.$apply()
            })

            $scope.openTran = function (tran) {
                openTran(tran.domain, tran.txid)
            }
        }
    })
}