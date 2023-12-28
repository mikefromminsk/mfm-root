function openOptionsDialog($rootScope, coin, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.coin = coin
            var domain = coin.domain
            $scope.username = wallet.address()
            $scope.contract = contract

            function checkFavorite() {
                $scope.isFavorite = storage.getStringArray(storageKeys.domains).indexOf(domain) != -1
            }

            checkFavorite()

            $scope.tabs = ["Info", "Transactions"]
            $scope.activeTabIndex = 0
            $scope.selectTab = function ($index) {
                $scope.activeTabIndex = $index
                if ($scope.activeTabIndex == 1)
                    $scope.trans()
            }

            post("/wallet/api/profile.php", {
                domain: domain
            }, function (response) {
                $scope.profile = response
                $scope.$apply()
            })

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
                openSendDialog(domain, success)
            }

            $scope.giveaway = function () {
                postContract(domain, contract.drop, {
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
                openInvite($rootScope, domain, success)
            }

            $scope.trans = function () {
                openTransactions(domain)
            }

            $scope.openDeposit = function () {
                $rootScope.openDeposit()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}