function openCraft(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/craft/recipe/craft/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.domain = domain

            $scope.amountResult = 1
            $scope.changeAmountResult = function () {
                $scope.amount1 = $scope.amountResult
                $scope.amount2 = $scope.amountResult
            }
            $scope.changeAmountResult()

            $scope.create = function () {
                getPin(function (pin) {
                    wallet.calcPassList(Object.keys($scope.recipe), pin, function (passes) {
                        postContract("craft", "api/craft.php", {
                            domain: domain,
                            address: wallet.address(),
                            components: JSON.stringify(passes)
                        }, function () {
                            showSuccessDialog("Crafted", $scope.hide)
                        }, (error) => {
                            if (error.indexOf("receiver doesn't exist") != -1) {
                                regAddress(domain, $scope.create)
                            }
                        })
                    })
                })
            }

            function loadRecipe() {
                postContract("craft", "api/recipe.php", {
                    domain: domain,
                }, function (response) {
                    $scope.recipe = response.recipe
                    $scope.$apply()
                })
            }

            function init() {
                loadRecipe()
            }

            init()

        }
    }).then(function () {
        if (success)
            success()
    })
}