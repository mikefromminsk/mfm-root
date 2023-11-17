function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.menuIndex = 0

    $scope.login = function () {
        loginFunction(updateCoins)
    }

    $scope.options = function (domain) {
        openOptionsDialog($scope, domain, updateCoins)
    }

    $scope.logout = function () {
        wallet.logout()
        init()
        search()
        showSuccess("Success logout")
    }

    $scope.newCoin = function () {
        openLaunchDialog($scope, $scope.search_text, updateCoins)
    }

    $scope.init = function () {
        $scope.drops = storage.getObject(storageKeys.drops, [])
        $scope.activeDomains = storage.getStringArray(storageKeys.domains)
        $scope.searchDomains = []
        search("")
    }
    $scope.init()
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
    $scope.$watch('search_text', search)

    function updateCoins() {
        var domains = {}
        domains[wallet.gas_domain] = true
        for (const domain of $scope.activeDomains)
            domains[domain] = true
        for (const domain of $scope.searchDomains)
            domains[domain] = true
        for (const drop of $scope.drops)
            domains[drop.domain] = true
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
            init()
        }
    }

    setInterval(function () {
        updateCoins()
    }, DEBUG ? 20000 : 10000)

    if (storage.getString("email") != "" && wallet.password == "") {
        loginFunction(updateCoins)
    }

    if (storage.getString("invite_key") != "") {
        var invite_key = storage.getString("invite_key")
        var isExist = false
        for (const drop of $scope.drops)
            if (drop.invite_key == invite_key)
                isExist = true
        if (!isExist) {
            $scope.drops.push({
                domain: storage.getString("domain"),
                invite_key: storage.getString("invite_key"),
            })
            storage.setObject(storageKeys.drops, $scope.drops)
        }
    }

    $scope.receiveDrop = function (drop) {
        if (wallet.username == "") {
            wallet.auth(function () {
                hasBalance(wallet.gas_domain, function () {
                    hasToken(drop.domain, function () {
                        requestBonus()
                    })
                })
            })
        }

        function requestBonus() {
            wallet.auth(function (username) {
                postContractWithGas(drop.domain, contract.bonus_receive, {
                    to_address: username,
                    invite_key: drop.invite_key,
                }, function () {
                    var drops = []
                    for (const item of storage.getObject(storageKeys.drops, []))
                        if (item.invite_key != drop.invite_key)
                            drops.push(item)
                    storage.setObject(storageKeys.drops, drops)
                    updateCoins()
                    showSuccessDialog("You have been received " + drop.amount + " " + drop.domain, updateCoins)
                })
            })
        }
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
    $scope.wallet = wallet
}