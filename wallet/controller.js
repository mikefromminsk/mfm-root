function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.menuIndex = 0

    $scope.login = function () {
        loginFunction(init)
    }

    $scope.options = function (domain) {
        openOptionsDialog($scope, domain, init)
    }

    $scope.openAccount = function () {
        openAccount(init)
    }

    $scope.openMessages = function () {
        openMessages(init)
    }

    $scope.newCoin = function () {
        openLaunchDialog($scope.search_text, init)
    }

    function init() {
        $scope.activeDomains = storage.getStringArray(storageKeys.domains)
        $scope.searchDomains = []
        $scope.bonuses = bonusesParse()
        search("")
    }
    $scope.drops = []
    $scope.coins = {}

    function search(newValue) {
        if (newValue == null) return
        post('/wallet/api/search.php', {
            search_text: (newValue || "")
        }, function (response) {
            var searchDomains = []
            for (const domain of response.result)
                if ($scope.activeDomains.indexOf(domain) == -1)
                    searchDomains.push(domain)
            $scope.searchDomains = searchDomains
            updateCoins()
        })
    }

    $scope.search_text = ''
    $scope.$watch('search_text', search)

    function updateCoins() {
        var domains = {}
        domains[wallet.gas_domain] = true
        for (const domain of $scope.activeDomains)
            domains[domain] = true
        for (const domain of $scope.searchDomains)
            domains[domain] = true
        for (const bonus of $scope.bonuses)
            domains[bonus.domain] = true
        post("/wallet/api/list.php", {
            domains: Object.keys(domains).join(","),
            address: wallet.username,
        }, function (response) {
            for (const coin of response.result)
                $scope.coins[coin.domain] = coin
            $scope.showBody = true
            $scope.$apply()
        })
    }

    $scope.totalBalance = function () {
        var totalBalance = 0
        for (const domain of $scope.activeDomains.values()) {
            var coin = $scope.coins[domain]
            if (coin != null)
                totalBalance += coin.price * coin.balance
        }
        return totalBalance
    }

    $scope.toggleFavorite = function (domain) {
        wallet.auth(function (username) {
            postContract(domain, contract.wallet, {
                address: username
            }, function () {
                addToStorage(domain)
            }, function () {
                postContractWithGas(domain, contract.reg, {
                    address: username,
                    next_hash: wallet.calcStartHash(domain + "/wallet")
                }, function () {
                    addToStorage(domain)
                })
            })
        })


        function addToStorage(domain) {
            if (!storage.isArrayItemExist(storageKeys.domains, domain)) {
                storage.pushToArray(storageKeys.domains, domain)
            } else {
                storage.removeFromArray(storageKeys.domains, domain)
            }
            $scope.activeDomains = storage.getStringArray(storageKeys.domains)
            $scope.search_text = ""
            $scope.$apply()
        }
    }

    setInterval(function () {
        updateCoins()
    }, DEBUG ? 20000 : 10000)


    $scope.receiveBonus = function (bonus) {
        wallet.auth(function (username) {
            hasBalance(wallet.gas_domain, function () {
                hasToken(bonus.domain, function () {
                    postContractWithGas(bonus.domain, contract.bonus_receive, {
                        to_address: username,
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
        }, init)
    }

    function bonusesParse() {
        var result = []
        for (const bonus of storage.getStringArray(storageKeys.bonuses)) {
            result.push({
                bonus: bonus,
                domain: bonus.split(":")[0],
                key: bonus.split(":")[1],
            })
        }
        return result
    }

    var referrer = storage.getString("referrer")
    if (referrer != "") {
        showSuccess(referrer)
        storage.pushToArray(storageKeys.bonuses, referrer)
        storage.setString("bonus", "")
    }
    $scope.wallet = wallet
    init()
}