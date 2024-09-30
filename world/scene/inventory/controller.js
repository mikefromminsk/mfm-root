function openInventory(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/inventory/index.html',
        controller: function ($scope) {
            addFormats($scope)

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
                                postContractWithGas("world", "api/token_deposit.php", {
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

            $scope.mode = ""
            $scope.setMode = function (mode) {
                $scope.mode = mode
                if (mode == "tokens") {
                    inventory("")
                } else if (mode == "recipes") {
                    recipes()
                }
            }
            $scope.setMode("tokens")

            function inventory(){
                postContract("world", "api/inventory.php", {
                    path: `avatar/${wallet.address()}`
                }, function (response) {
                    $scope.inventory = response.inventory
                    $scope.$apply()
                })
            }

            $scope.recipes = {}
            function recipes() {
                postContract("world", "api/settings.php", {
                }, function (response) {
                    $scope.recipes = response.recipe
                    $scope.$apply()
                })
            }

            $scope.openCraft = function (domain) {
                openCraft(domain, openInventory)
            }
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}