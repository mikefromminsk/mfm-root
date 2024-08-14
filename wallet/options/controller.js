function openOptions($rootScope, coin, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope) {
            addFormats($scope)
            var domain = coin.domain
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

            $scope.ico_sell = function () {
                openIcoSell($rootScope, domain, init)
            }

            $scope.ico_buy = function () {
                openIcoBuy($rootScope, domain, init)
            }

            $scope.share = function () {
                openInvite(domain, success)
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

            var chart
            var lineSeries

            function initChart() {
                setTimeout(function () {
                    if (chart == null) {
                        var tradeChart = document.getElementById("priceChart")
                        chart = LightweightCharts.createChart(tradeChart, {
                            layout: {
                                background: { color: '#222' },
                                textColor: '#DDD',
                            },
                            grid: {
                                vertLines: { color: '#444' },
                                horzLines: { color: '#444' },
                            },
                            crosshair: {
                                mode: LightweightCharts.CrosshairMode.Normal,
                            },
                        });
                        lineSeries = chart.addLineSeries();
                        new ResizeObserver(entries => {
                            if (entries.length === 0 || entries[0].target !== tradeChart) return;
                            const newRect = entries[0].contentRect;
                            chart.applyOptions({height: newRect.height, width: newRect.width});
                        }).observe(tradeChart)
                    }
                    $scope.loadChart()
                })
            }

            function loadChart() {
                postContract("exchange", "api/exchange/chart.php", {
                    domain: domain,
                    key: "price",
                    period_name: "1M",
                }, function (response) {
                    for (var i = 0; i < response.chart.length; i++) {
                        response.chart[i].time =
                            new Date(response.chart[i].time * 1000 + (i * 60 * 60 * 24 * 1000)).toJSON().slice(0, 10)
                    }
                    console.log(response.chart)
                    lineSeries.setData(response.chart)
                })
            }
            function init() {
                postContract("wallet", "api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.coin = response
                    $scope.$apply()
                })
                initChart()
                loadChart()
            }

            setInterval(function () {
                loadChart()
            }, 3000)

            init()
        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}