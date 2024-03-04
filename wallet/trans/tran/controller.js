function openTran(domain, txid, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/trans/tran/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain

            post("/wallet/api/tran.php", {
                domain: domain,
                txid: txid,
            }, function (response) {
                $scope.tran = response
            })

            $scope.copy = function (txid) {
                $scope.copyText(txid)
                showSuccess("TxId copied")
            }

            $scope.getTxLink = function (txid) {
                return location.origin + "/explorer?domain=" + domain + "&txid=" + txid
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}