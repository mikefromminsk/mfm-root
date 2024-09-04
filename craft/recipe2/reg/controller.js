function openRegRecipe2(domain, success) {
    window.$mdDialog.show({
        templateUrl: '/craft/recipe2/reg/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.domain1 = "stone"
            $scope.domain2 = "wood"

            $scope.create = function () {
                getPin(function (pin) {
                    wallet.calcPass($scope.domain1, pin, function (pass1) {
                        wallet.calcPass($scope.domain2, pin, function (pass2) {
                            postContractWithGas(domain, "api/craft/recipe2.php", {
                                domain1: $scope.domain1,
                                pass1: pass1,
                                domain2: $scope.domain2,
                                pass2: pass2,
                            }, regRecipe, regRecipe)
                        })
                    })
                })

            }

            function regRecipe(){
                postContractWithGas("wallet", "token/api/regRecipe.php",{
                    domain: domain,
                    type: "recipe2",
                }, function () {
                    showSuccessDialog("Recipe created", $scope.hide)
                })
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}