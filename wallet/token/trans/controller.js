function addTransactions($scope) {

    function loadTrans() {
        postContract("token", "trans.php", {
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