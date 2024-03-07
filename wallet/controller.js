function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog
    $scope.wallet = wallet

    $scope.menuIndex = 0

    $scope.login = function () {
        openLogin(init)
    }

    $scope.options = function (coin) {
        openOptionsDialog($scope, coin, init)
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

    $scope.openUsdtTransactions = function () {
        openUsdtTransactions(init)
    }

    function init() {
        updateBonuses()
        updateCoins()
        recommendations()
    }

    $scope.drops = []
    $scope.coins = {}
    $scope.bonuses = []
    $scope.bonusesCoins = {}

    function updateBonuses() {
        $scope.bonuses = []
        var domains = []
        for (const bonus of storage.getStringArray(storageKeys.bonuses)) {
            var domain = bonus.split(":")[0]
            var key = bonus.split(":")[1]
            $scope.bonuses.push({domain: domain, key: key})
            if (domains.indexOf(domain) == -1 && $scope.bonusesCoins[domain] == null)
                domains.push(domain)
        }
        if (domains.length > 0) {
            post("/wallet/api/list.php", {
                domains: domains.join(","),
            }, function (response) {
                $scope.bonusesCoins = {}
                for (let coin of response.result)
                    $scope.bonusesCoins[coin.domain] = coin
                $scope.$apply()
            })
        }
    }

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
                    || $scope.selectedCategories.indexOf('mining') != -1 && token.mining)
                    result.push(token)
        }
        return result
    }

    function checkTransfers(from, to) {
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
                    if (toToken.balance > fromToken.balance){
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
                if ($scope.activeCoins != null)
                    checkTransfers($scope.activeCoins, response.result)
                $scope.activeCoins = response.result
                $scope.filteredActiveCoins = filterByCategory(response.result)
                $scope.showBody = true
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


    $scope.receiveBonus = function (bonus) {
        hasBalance(wallet.gas_domain, function () {
            hasToken(bonus.domain, function () {
                postContractWithGas(bonus.domain, "api/token/bonus_receive.php", {
                    to_address: wallet.address(),
                    invite_key: bonus.key,
                }, function (response) {
                    storage.removeFromArray(storageKeys.bonuses, bonus.bonus)
                    showSuccessDialog("You have been received " + $scope.formatAmount(response.received, bonus.domain), init)
                }, function () {
                    storage.removeFromArray(storageKeys.bonuses, bonus.bonus)
                    showInfoDialog("Bonus is invalid", init)
                })
            }, init)
        }, init)
    }

    var bonus = storage.getString("bonus")
    if (bonus != "") {
        if (!storage.isArrayItemExist(storageKeys.bonuses, bonus)) {
            storage.pushToArray(storageKeys.bonuses, bonus)
        } else {
            showInfoDialog("Bonus " + bonus + " was checked before")
        }
    }

    $scope.categories = Object.keys(window.tokenCategories)
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

    if (storage.getString(storageKeys.onboardingShowed) == "") {
        storage.setString(storageKeys.onboardingShowed, "true")
        openOnboardingDialog(init)
    }

    init()
}

function coinListController($scope){

}