function regAddress(domain, success) {
    getPin(function (pin) {
        calcPass(domain, pin, function (pass) {
            postContract("token", "send.php", {
                domain: domain,
                from_address: "owner",
                to_address: wallet.address(),
                amount: 0,
                pass: pass
            }, function () {
                if (success)
                    success()
            })
        })
    })
}

function addTokens($scope) {
    $scope.searchMode = false
    $scope.menuIndex = 0

    $scope.toggleSearchMode = function () {
        $scope.searchMode = !$scope.searchMode
    }

    if (getParam("domain")) {
        storage.setString(storageKeys.selectedCoin, getParam("domain"))
    }

    $scope.login = function () {
        openLogin(init)
    }

    $scope.openTokenProfile = function (domain) {
        openTokenProfile(domain, function (result) {
            if (result && result.action == "store") {
                $scope.selectedToken = domain
                $scope.selectTab(1)
            } else {
                init()
            }
        })
    }

    $scope.openAccount = function () {
        openAccount(init)
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

    $scope.openSupport = function () {
        window.open("https://t.me/+UWS_ZfqIi1tkNmVi", init)
    }

    function tokens(search_text) {
        postContract("wallet", "token/api/tokens.php", {
            address: wallet.address(),
            search_text: search_text,
        }, function (response) {
            $scope.activeTokens = response.active
            $scope.recTokens = response.recs
            $scope.showBody = true
            $scope.$apply()
        })
    }

    $scope.search_text = ''
    $scope.$watch('search_text', function (newValue) {
        if (newValue == null) return
        tokens(newValue)
    })

    $scope.getTotalBalance = function () {
        var totalBalance = 0
        if ($scope.activeTokens != null)
            for (const token of $scope.activeTokens)
                totalBalance += token.price * token.balance
        return totalBalance
    }

    $scope.regAddress = function (domain) {
        regAddress(domain, init)
    }

        subscribe("transactions", function (data) {
            if (data.to == wallet.address()) {
                showSuccess("You received " + $scope.formatAmount(data.amount, data.domain))
                setTimeout(function () {
                    new Audio("/wallet/dialogs/success/payment_success.mp3").play()
                })
                tokens("")
            }
        });

    subscribe("place", function (data) {
        function updateTokens(tokenList, domain, price) {
            if (tokenList != null)
                for (let token of tokenList) {
                    if (token.domain == domain) {
                        token.price = price
                        $scope.$apply()
                        break;
                    }
                }
        }

        updateTokens($scope.activeTokens, data.domain, data.price)
        updateTokens($scope.recTokens, data.domain, data.price)
    });

    $scope.mode = "tokens"
    $scope.setMode = function (mode) {
        $scope.mode = mode
        if (mode == "tokens") {
            tokens("")
        } else if (mode == "recipes") {
            recipes("")
        }
    }

    $scope.recipes = {}

    function recipes(search_text) {
        post("/wallet/token/api/recepes.php", {}, function (response) {
            $scope.recipes = response.recipes
            $scope.$apply()
        })
    }

    $scope.openCraft2 = function (recipe) {
        openCraft(recipe, init)
    }

    $scope.openWorld = function () {
        window.open("/world", '_blank').focus();
    }

    function init() {
        $scope.setMode($scope.mode)
    }

    init()
}