function openTransactions(domain) {
    window.$mdDialog.show({
        templateUrl: '/wallet/trans/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.username = wallet.address()

            $scope.fromDate = ""
            $scope.toDate = ""

            post("/wallet/api/trans_user.php", {
                domain: domain,
                address: wallet.address(),
            }, function (response) {
                $scope.trans = $scope.groupByTimePeriod(response.trans)
                $scope.$apply()
            })

            $scope.selectFrom = function () {
                selectDate(function (date) {
                    $scope.fromDate = date
                })
            }

            $scope.selectTo = function () {
                selectDate(function (date) {
                    $scope.toDate = date
                })
            }

            $scope.search = function () {
                post("/wallet/api/trans_user.php", {
                    domain: domain,
                    address: wallet.address(),
                    fromDate: Math.ceil($scope.fromDate.getTime() / 1000),
                    toDate: Math.ceil($scope.toDate.getTime() / 1000),
                }, function (response) {
                    $scope.trans = groupByTimePeriod(response.trans)
                    $scope.$apply()
                })
            }

            $scope.openTran = function (tran) {
                openTran(tran.domain, tran.txid)
            }
        }
    })
}