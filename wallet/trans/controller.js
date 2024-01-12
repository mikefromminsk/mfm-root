function openTransactions(domain) {
    window.$mdDialog.show({
        templateUrl: '/wallet/trans/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.username = wallet.address()

            $scope.fromDate = ""
            $scope.toDate = ""

            var groupByTimePeriod = function (obj) {
                var objPeriod = {};
                var oneDay = 24 * 60 * 60;
                for (var i = 0; i < obj.length; i++) {
                    var d = new Date(obj[i]['time']);
                    d = Math.floor(d.getTime() / oneDay);
                    objPeriod[d] = objPeriod[d] || [];
                    objPeriod[d].push(obj[i]);
                }
                return objPeriod;
            }

            post("/wallet/api/trans.php", {
                domain: domain,
                address: wallet.address(),
            }, function (response) {
                $scope.trans = groupByTimePeriod(response.trans)
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
                post("/wallet/api/trans.php", {
                    domain: domain,
                    address: wallet.address(),
                    fromDate: Math.ceil($scope.fromDate.getTime() / 1000),
                    toDate: Math.ceil($scope.toDate.getTime() / 1000),
                }, function (response) {
                    $scope.trans = groupByTimePeriod(response.trans)
                    $scope.$apply()
                })
            }
        }
    })
}