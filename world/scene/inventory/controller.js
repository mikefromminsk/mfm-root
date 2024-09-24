function openInventory(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/inventory/index.html',
        controller: function ($scope) {
            addFormats($scope)

            dataObject("world/avatar/" + wallet.address() + "/inventory", (inventory) => {
                $scope.inventory = inventory
                $scope.$apply()
            })

            $scope.openDeposit = function () {
                openWorldDeposit(openInventory)
            }

            $scope.depositAll = function () {
                postContract("wallet", "token/api/tokens.php", {
                    address: wallet.address(),
                }, (response) => {
                    getPin((pin) => {
                        response.active.forEach((token) => {
                            wallet.calcPass(token.domain, pin, (pass) => {
                                postContractWithGas("world", "api/deposit.php", {
                                    address: wallet.address(),
                                    domain: token.domain,
                                    amount: token.balance,
                                    pass: pass,
                                })
                            })
                        })
                        showSuccessDialog("Deposits successful", openInventory)
                    })
                })
            }
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}