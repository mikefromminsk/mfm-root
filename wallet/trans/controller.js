function addTransactions($scope) {

    function loadTrans() {
        post("/wallet/api/trans_user.php", {
            address: wallet.address(),
        }, function (response) {
            $scope.trans = $scope.groupByTimePeriod(response.trans)
            $scope.$apply()
        })
    }

    $scope.openTran = function (tran) {
        openTran(tran.domain, tran.txid)
    }

    loadTrans()
}