function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.wallet = wallet
    $scope.apps = {}
    $scope.selectedCoin
    $scope.searchMode = false

    $scope.menuIndex = 0

    $scope.toggleSearchMode = function () {
        $scope.searchMode = !$scope.searchMode
    }

    if ($scope.getUriParam("domain")) {
        storage.setString(storageKeys.selectedCoin, $scope.getUriParam("domain"))
    }

    $scope.login = function () {
        openLogin(init)
    }

    $scope.options = function (coin) {
        openOptions($scope, coin, function (result) {
            if (result && result.action == "store") {
                $scope.selectCoin(coin)
            } else {
                init()
            }
        })
    }

    $scope.openAccount = function () {
        openAccount(init)
    }

    $scope.openTransactions = function () {
        openTransactions(wallet.gas_domain)
    }

    $scope.newCoin = function () {
        openLaunchDialog($scope.search_text, init)
    }

    $scope.openDeposit = function () {
        openDeposit(init)
    }

    $scope.openWithdrawal = function () {
        openWithdrawal(init)
    }

    function init() {
        updateBonuses()
        updateCoins()
        recommendations()
        searchApp()
        loadTrans()
    }

    $scope.drops = []
    $scope.coins = {}
    $scope.bonuses = []

    function recommendations() {
        post("/wallet/api/search.php", {
            search_text: "",
        }, function (response) {
            $scope.recommendedCoins = []
            var domains = storage.getStringArray(storageKeys.domains)
            for (const coin of response.result)
                if (domains.indexOf(coin.domain) == -1)
                    $scope.recommendedCoins.push(coin)
            $scope.recommendedCoins = filterByCategory($scope.recommendedCoins)
            $scope.$apply()
        })
    }

    $scope.search_text = ''
    $scope.$watch('search_text', function (newValue) {
        if (newValue == null) return
        post("/wallet/api/search.php", {
            search_text: (newValue || ""),
        }, function (response) {
            $scope.searchCoins = response.result
            for (const coin of $scope.searchCoins)
                coin.isFavorite = storage.isArrayItemExist(storageKeys.domains, coin.domain)
            $scope.$apply()
        })
    })

    function filterByCategory(tokens) {
        var result = tokens
        if ($scope.selectedCategories.length > 0) {
            result = []
            for (let token of tokens)
                if ($scope.selectedCategories.indexOf(token.category) != -1
                    || $scope.selectedCategories.indexOf('mining') != -1 && token.mining
                    || $scope.selectedCategories.indexOf('my_coins') != -1 && token.owner == wallet.address())
                    result.push(token)
        }
        return result
    }

    function checkTransfers(from, to) {
        if (from.length == 0) return

        function showTopMessage(balanceChange, domain) {
            $mdToast.show($mdToast.simple().position('top').textContent(
                "You received " + $scope.formatAmount(balanceChange, domain)
            ))
            setTimeout(function () {
                new Audio("/wallet/dialogs/success/payment_success.mp3").play()
            })
        }

        for (let toToken of to) {
            var found = false
            for (let fromToken of from) {
                if (toToken.domain == fromToken.domain) {
                    found = true
                    if (toToken.balance > fromToken.balance) {
                        showTopMessage(toToken.balance - fromToken.balance, toToken.domain)
                    }
                }
            }
            if (!found && toToken.balance > 0) {
                showTopMessage(toToken.balance, toToken.domain)
            }
        }
    }

    function updateCoins() {
        var domains = storage.getStringArray(storageKeys.domains)
        if (domains.length > 0) {
            post("/wallet/api/list.php", {
                domains: domains.join(","),
                address: wallet.address(),
            }, function (response) {
                $scope.coin = response.gas
                if ($scope.activeCoins != null)
                    checkTransfers($scope.activeCoins, response.result)
                $scope.activeCoins = response.result
                $scope.filteredActiveCoins = filterByCategory(response.result)
                $scope.showBody = true
                let selectedCoin = storage.getString(storageKeys.selectedCoin)
                for (let coin of response.result) {
                    if (coin.owner == wallet.address()) {
                        if (coin.domain == selectedCoin)
                            $scope.selectedCoin = coin
                        if (selectedCoin == '' && $scope.selectedCoin == null)
                            $scope.selectedCoin = coin
                    }
                }
                $scope.$apply()
            })
        } else {
            $scope.activeCoins = []
            $scope.filteredActiveCoins = []
            $scope.showBody = true
        }
    }

    $scope.totalBalance = function () {
        var totalBalance = 0
        if ($scope.activeCoins != null)
            for (const coin of $scope.activeCoins)
                totalBalance += coin.price * coin.balance
        return totalBalance
    }

    $scope.addFavorite = function (domain, success) {
        postContract(domain, "api/token/wallet.php", {
            address: wallet.address()
        }, function () {
            addToStorage(domain)
        }, function () {
            postContractWithGas(domain, "api/token/reg.php", function (key) {
                return {
                    address: wallet.address(),
                    next_hash: md5(key)
                }
            }, function () {
                addToStorage(domain)
            })
        })

        function addToStorage(domain) {
            if (!storage.isArrayItemExist(storageKeys.domains, domain)) {
                storage.pushToArray(storageKeys.domains, domain)
            } else {
                storage.removeFromArray(storageKeys.domains, domain)
            }
            $scope.search_text = ""
            if (success)
                success()
        }
    }

    $scope.toggleFavorite = function (domain) {
        $scope.addFavorite(domain, init)
    }

    setInterval(function () {
        updateCoins()
    }, DEBUG ? 20000 : 10000)


    function updateBonuses() {
        if (storage.getString(storageKeys.bonuses) != "") {
            post("/wallet/api/bonuses.php", {
                bonuses: storage.getString(storageKeys.bonuses),
            }, function (response) {
                $scope.bonuses = response.bonuses
                if (response.bonuses == null) {
                    $scope.bonus_coins = {}
                } else {
                    $scope.bonus_coins = Object.fromEntries(
                        response.result.map(o => [o.domain, o])
                    )
                }
                $scope.$apply()
            })
        }
    }

    $scope.receiveBonus = function (bonus) {
        if (wallet.address() == "") {
            openLogin(init)
        } else {
            postContractWithGas(bonus.domain, "api/token/invite/receive.php", {
                to_address: wallet.address(),
                invite_key: bonus.bonus_key,
            }, function (response) {
                storage.removeFromArray(storageKeys.bonuses, bonus.bonus)
                setTimeout(function () {
                    updateBonuses()
                }, 3000)
                showSuccessDialog("You have been received " + $scope.formatAmount(response.received, bonus.domain), init)
            }, function () {
                //storage.removeFromArray(storageKeys.bonuses, bonus.bonus)
                showInfoDialog("Bonus is invalid", init)
            })
        }
    }

    var bonus = storage.getString("bonus")
    if (bonus != "") {
        if (!storage.isArrayItemExist(storageKeys.bonuses, bonus)) {
            storage.pushToArray(storageKeys.bonuses, bonus)
        }
    }

    $scope.selectedCategories = storage.getStringArray(storageKeys.categories)

    $scope.selectCategory = function (key) {
        if (storage.isArrayItemExist(storageKeys.categories, key)) {
            storage.removeFromArray(storageKeys.categories, key)
        } else {
            storage.pushToArray(storageKeys.categories, key)
        }
        $scope.selectedCategories = storage.getStringArray(storageKeys.categories)
        init()
    }

    $scope.clearCategoryFilter = function () {
        storage.setString(storageKeys.categories, "")
        $scope.selectedCategories = storage.getStringArray(storageKeys.categories)
        init()
    }

    /*if (storage.getString(storageKeys.onboardingShowed) == "") {
        storage.setString(storageKeys.onboardingShowed, "true")
        openOnboardingDialog(init)
    }*/

    $scope.openSupport = function () {
        window.open("https://t.me/+UWS_ZfqIi1tkNmVi", init)
    }


    // Store

    $scope.openAppSettings = function () {
        openAppSettings($scope.selectedCoin.domain, init)
    }

    $scope.openProfile = function (app) {
        if (app.installed) {
            if (app.console) {
                openWeb(location.origin + "/" + app.domain + "/console?domain=" + $scope.selectedCoin.domain, init)
            } else {
                window.open(location.origin + "/" + app.domain)
            }
        } else {
            openProfile(app)
        }
    }

    $scope.selectCoin = function (coin) {
        $scope.selectedCoin = coin
        storage.setString(storageKeys.selectedCoin, coin.domain)
        $scope.selectTab(1)
        searchApp()
    }

    $scope.installApp = function (app) {
        postContractWithGas("wallet", "api/install.php", {
            domain: $scope.selectedCoin.domain,
            app_domain: app.domain,
        }, function () {
            init()
            showSuccess("Install success")
        })
    }

    $scope.selectTab = function (tab) {
        $scope.selectedIndex = tab
        if (tab == 2) {
            loadTrans()
        }
    }

    function searchApp(newValue) {
        post("/wallet/api/apps.php", {
            search_text: (newValue || ""),
            domain: (storage.getString(storageKeys.selectedCoin) || ""),
        }, function (response) {
            $scope.apps = response.apps || {}
            $scope.$apply()
        })
    }

    //transactions
    function loadTrans() {
        post("/wallet/api/trans_user.php", {
            address: wallet.address(),
        }, function (response) {
            $scope.trans = $scope.groupByTimePeriod(response.trans)
            $scope.$apply()
        })
    }

    $scope.openTran = function (tran) {
        openTran(tran.domain, tran.txid)
    }

    init()
}