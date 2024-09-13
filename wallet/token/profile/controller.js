function getProfile(domain, success, error) {
    postContract("wallet", "api/profile.php", {
        domain: domain,
        address: wallet.address(),
    }, success, error)
}

function openTokenProfile(domain, success) {
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
                openDeposit()
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
                openSendDialog(domain, $scope.token.owner, "", init)
            }

            function loadProfile() {
                getProfile(domain, function (response) {
                    $scope.token = response
                    $scope.$apply()
                })
            }

            addChart($scope, domain + "_price")

            subscribe("place", function (data) {
                if (data.domain == domain) {
                    $scope.token.price = data.price
                    $scope.updateChart()
                    $scope.$apply()
                }
            })

            $scope.openChart = function (key) {
                openChart(key)
            }

            function init() {
                loadProfile()
            }

            init()
        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}