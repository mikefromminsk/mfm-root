function main($scope, $http, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.menuIndex = 0

    $scope.login = function () {
        loginFunction(init)
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
        showInfoDialog("Deposit is not available right now", init)
    }

    function init() {
        $scope.bonuses = bonusesParse()
        updateCoins()
        recommendations()
    }

    $scope.drops = []
    $scope.coins = {}

    function recommendations() {
        post("/wallet/api/search.php", {
            search_text: "",
        }, function (response) {
            $scope.recommendedCoins = []
            var domains = storage.getStringArray(storageKeys.domains)
            for (const coin of response.result)
                if (domains.indexOf(coin.domain) == -1)
                    $scope.recommendedCoins.push(coin)
            $scope.$apply()
        })
    }

    $scope.search_text = ''
    $scope.$watch('search_text', function (newValue) {
        if (newValue == null) return
        post("/wallet/api/search.php", {
            search_text: (newValue || ""),
        }, function (response) {
            $scope.searchCoins = []
            var domains = storage.getStringArray(storageKeys.domains)
            for (const coin of response.result)
                if (domains.indexOf(coin.domain) == -1)
                    $scope.searchCoins.push(coin)
            $scope.$apply()
        })
    })

    function updateCoins() {
        var domains = storage.getStringArray(storageKeys.domains)
        if (domains.length > 0) {
            post("/wallet/api/list.php", {
                domains: domains.join(","),
                address: wallet.username,
            }, function (response) {
                $scope.activeCoins = response.result
                $scope.showBody = true
                $scope.$apply()
            })
        } else {
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

    window.tokenCategories = {
        UNKNOWN: "Это цифровой актив, который используется для представления определенной ценности или права в блокчейн-системе. Он может быть использован для обеспечения безопасности и защиты данных, а также для доступа к определенным ресурсам или функциям в децентрализованной среде. Крипто токены могут быть созданы и управляться на основе различных стандартов, таких как ERC-20, ERC-721 и другие.",
        L1: "Токен для оплаты газа в блокчейне - это цифровой токен, который используется для оплаты комиссий за выполнение транзакций в сети блокчейн. Он является необходимым элементом для обеспечения работы сети и поддержания ее безопасности. Количество токенов, необходимых для выполнения транзакции, зависит от сложности операции и текущей загруженности сети.",
        STABLECOIN: "Stablecoin - это криптовалюта, которая призвана сохранять свою стоимость относительно определенного актива, такого как доллар США или золото. Она обычно используется для уменьшения волатильности криптовалютного рынка и обеспечения стабильности цены.",
    }

    if (storage.getString("onboarding_showed") == ""){
        storage.setString("onboarding_showed", "true")
        openOnboardingDialog(init)
    }
}