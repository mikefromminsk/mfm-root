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
        openTokenProfile($scope, domain, function (result) {
            if (result && result.action == "store") {
                $scope.selectToken(domain)
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

    function init() {
        tokens("")
        //searchApp()
        //loadTrans()
    }

    $scope.coins = {}

    function tokens(search_text) {
        post("/wallet/token/api/tokens.php", {
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
        getPin(function (pin) {
            calcPass(domain, pin, function (pass) {
                postContract("wallet", "reg.php", {
                    address: wallet.address(),
                    domain: domain,
                    next_hash: pass,
                }, init)
            })
        })
    }


    $scope.openSupport = function () {
        window.open("https://t.me/+UWS_ZfqIi1tkNmVi", init)
    }

    /*
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
    }*/

    function showTopMessage(message) {
        $mdToast.show($mdToast.simple().position('top').textContent(message))
        setTimeout(function () {
            new Audio("/wallet/dialogs/success/payment_success.mp3").play()
        })
    }

    subscribe("transactions", function (data) {
        if (data.tran.to == wallet.address()) {
            showTopMessage("You received " + $scope.formatAmount(data.tran.amount, data.tran.domain))
        }
    });

    init()
}