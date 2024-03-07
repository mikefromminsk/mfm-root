function openOptionsDialog($rootScope, coin, success) {
    window.$mdDialog.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.coin = coin
            var domain = coin.domain
            $scope.wallet = wallet
            $scope.contract = brc1
            $scope.siteExist = false

            function checkFavorite() {
                $scope.isFavorite = storage.getStringArray(storageKeys.domains).indexOf(domain) != -1
            }

            checkFavorite()

            post("/wallet/api/profile.php", {
                domain: domain
            }, function (response) {
                $scope.profile = response
                $scope.$apply()
            })

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

            $scope.categoriesDesc = tokenCategories

            $scope.toggleFavorite = function () {
                $rootScope.addFavorite(domain, function () {
                    checkFavorite()
                    $scope.$apply()
                })
            }

            getContracts(domain, function (contracts) {
                $scope.contracts = contracts
                $scope.$apply()
            })

            $scope.sendDialog = function () {
                openSendDialog(domain, "", "", success)
            }

            $scope.openMining = function () {
                openMining(domain, success)
            }

            $scope.openStore = function () {
                openWeb(location.origin + "/store/?domain=" + domain)
            }

            $scope.giveaway = function () {
                postContract(domain, "api/token/drop.php", {
                    address: wallet.address()
                }, function (response) {
                    showSuccessDialog("You have been received " + $scope.formatAmount(response.dropped, domain), success)
                })
            }

            $scope.ico_sell = function () {
                openIcoSell($rootScope, domain, success)
            }

            $scope.ico_buy = function () {
                openIcoBuy($rootScope, domain, success)
            }

            $scope.share = function () {
                openInvite(domain, success)
            }

            $scope.trans = function () {
                openTransactions(domain)
            }

            $scope.openDeposit = function () {
                $rootScope.openDeposit()
            }

            $scope.openSite = function () {
                openWeb("/" + domain)
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}