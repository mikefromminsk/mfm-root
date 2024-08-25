function openTokenProfile($rootScope, domain, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/token/profile/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.siteExist = false

            function checkFavorite() {
                $scope.isFavorite = storage.getStringArray(storageKeys.domains).indexOf(domain) != -1
            }

            checkFavorite()

            function checkSiteExist(domain) {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "/" + domain + "/index.html", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === XMLHttpRequest.DONE) {
                        $scope.siteExist = xhr.status === 200;
                        $scope.$apply()
                    }
                }
                xhr.send(null);
            }

            checkSiteExist(domain)

            $scope.toggleFavorite = function () {
                $rootScope.addFavorite(domain, function () {
                    checkFavorite()
                    $scope.$apply()
                })
            }

            $scope.sendDialog = function () {
                openSendDialog(domain, "", "", init)
            }

            $scope.openMining = function () {
                openWeb(location.origin + "/mining/console?domain=" + domain, init)
            }

            $scope.openStore = function () {
                $scope.close({
                    action: "store",
                    domain: domain
                })
            }

            $scope.sell = function () {
                openExchange(domain, 1, init)
            }

            $scope.buy = function () {
                openExchange(domain, 0, init)
            }

            $scope.share = function () {
                openShare(domain, success)
            }

            $scope.openDeposit = function () {
                $rootScope.openDeposit()
            }

            $scope.openSite = function () {
                window.open("/" + domain)
            }

            $scope.openTokenSettings = function () {
                openTokenSettings(domain, function (result) {
                    if (result == "success")
                        location.reload()
                })
            }

            $scope.openWithdrawal = function () {
                openWithdrawal(init)
            }

            $scope.donate = function () {
                openSendDialog(domain, $scope.coin.owner, "", init)
            }

            var lineSeries

            function initChart() {
                setTimeout(function () {
                    if (lineSeries == null) {
                        lineSeries = createChart("priceChart").addLineSeries();
                    }
                    $scope.loadChart()
                })
            }

            function loadChart() {
                postContract("exchange", "utils/chart.php", {
                    domain: domain,
                    key: "price",
                    period_name: "1M",
                }, function (response) {
                    for (var i = 0; i < response.chart.length; i++) {
                        response.chart[i].time =
                            new Date(response.chart[i].time * 1000 + (i * 60 * 60 * 24 * 1000)).toJSON().slice(0, 10)
                    }
                    lineSeries.setData(response.chart)
                })
            }
            function init() {
                loadProfile()
                initChart()
                loadChart()
            }

            function loadProfile() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.token = response
                    $scope.$apply()
                })
            }
           /* setInterval(function () {
                loadChart()
            }, 3000)*/

            init()
        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}