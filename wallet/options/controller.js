function openOptionsDialog($rootScope, domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/options/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.coin = $rootScope.coins[domain]
            $scope.username = wallet.username
            $scope.contract = contract

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
                $scope.profile = response;
            })

            $scope.categoriesDesc = {
                L1: "Токен для оплаты газа в блокчейне - это цифровой токен, который используется для оплаты комиссий за выполнение транзакций в сети блокчейн. Он является необходимым элементом для обеспечения работы сети и поддержания ее безопасности. Количество токенов, необходимых для выполнения транзакции, зависит от сложности операции и текущей загруженности сети.",
                STABLECOIN: "Stablecoin - это криптовалюта, которая призвана сохранять свою стоимость относительно определенного актива, такого как доллар США или золото. Она обычно используется для уменьшения волатильности криптовалютного рынка и обеспечения стабильности цены.",
            }

            $scope.toggleFavorite = function () {
                $scope.coin.favorite = !$scope.coin.favorite
            }

            getContracts(domain, function (contracts) {
                $scope.contracts = contracts
                $scope.$apply()
            })

            $scope.sendDialog = function () {
                openSendDialog(domain, success)
            }

            $scope.giveaway = function () {
                wallet.auth(function (username) {
                    postContract(domain, contract.drop, {
                        address: username
                    }, function () {
                        success()
                        $mdBottomSheet.hide()
                    })
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

            $scope.contact = function () {
                openMessages($scope.coin.owner, domain)
            }

            $scope.back = function (){
                $mdBottomSheet.hide()
            }
        }
    })
}