function openOptions($rootScope, coin, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope) {
            addFormats($scope)
            var domain = coin.domain
            $scope.wallet = wallet
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
                openSendDialog(domain, "", "", success)
            }

            $scope.openMining = function () {
                openMining(domain, success)
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

            function init() {
                post("/wallet/api/profile.php", {
                    domain: domain,
                    address: wallet.address(),
                }, function (response) {
                    $scope.coin = response
                    $scope.$apply()
                })
            }

            init()
        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}