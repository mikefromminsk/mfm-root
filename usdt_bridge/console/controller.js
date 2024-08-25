function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    $scope.domain = getParam("domain")
    $scope.balances = {}

    function getBalance(domain, address) {
        dataGet(domain + "/wallet/" + address + "/amount", function (amount) {
            $scope.balances[address] = {
                amount: amount,
                address: address,
                domain: domain,
            }
            $scope.$apply()
        })
    }

    $scope.initilize = function () {
        postContractWithGas(wallet.quote_domain, "api/init.php", {},function () {
            showSuccess("Initilized", init)
        })
    }

    function init() {
        getBalance(wallet.quote_domain, "usdt_withdrawals")
        getBalance(wallet.gas_domain, "usdt_withdrawal_success")
        getBalance(wallet.gas_domain, "usdt_withdrawal_start")
        getBalance(wallet.quote_domain, "usdt_deposits")
        getBalance(wallet.gas_domain, "usdt_deposit_check")
        getBalance(wallet.gas_domain, "usdt_deposit_start")
    }

    $scope.send = function (domain, address, amount) {
        openSendDialog(domain, address, amount, init)
    }

    init()
}