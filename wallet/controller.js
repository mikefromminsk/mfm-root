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
        search()
        showSuccess("Success logout")
    }

    $scope.newCoin = function () {
        openLaunchDialog($scope, $scope.search_text, updateCoins)
    }

    $scope.init = function () {
        $scope.activeDomains = storage.getStringArray(storageKeys.domains)
        $scope.searchDomains = []
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

    $scope.$watch('search_text', search)

    function updateCoins() {
        var domains = {}
        domains[wallet.gas_domain] = true
        for (const domain of $scope.activeDomains)
            domains[domain] = true
        for (const domain of $scope.searchDomains)
            domains[domain] = true
        for (const bonus of storage.getStringArray(storageKeys.bonuses))
            domains[bonus.split(":")[0]] = true
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


    $scope.receiveDrop = function (bonus) {
        var domain = bonus.split(":")[0]
        var key = bonus.split(":")[1]
        if (wallet.username == "") {
            wallet.auth(function () {
                hasBalance(wallet.gas_domain, function () {
                    hasToken(domain, function () {
                        requestBonus()
                    })
                })
            })
        }

        function requestBonus() {
            wallet.auth(function (username) {
                postContractWithGas(domain, contract.bonus_receive, {
                    to_address: username,
                    invite_key: key,
                }, function (response) {
                    storage.removeFromArray(storageKeys.bonuses, bonus)
                    showSuccessDialog("You have been received " + $scope.formatAmount(response.received, domain), updateCoins)
                })
            })
        }
    }

    if (storage.getString("bonus") != "") {
        storage.pushToArray(storageKeys.bonuses, storage.getString("bonus"))
        storage.setString("bonus", "")
    }

    $scope.wallet = wallet
    $scope.init()
}