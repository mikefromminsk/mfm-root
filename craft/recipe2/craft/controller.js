function openCraft2(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/craft/recipe2/craft/index.html',
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
                    wallet.calcPass($scope.recipe2.domain1, pin, function (pass1) {
                        wallet.calcPass($scope.recipe2.domain2, pin, function (pass2) {
                            postContract("craft", "api/craft2.php", {
                                address: wallet.address(),
                                domain: domain,
                                domain1: $scope.recipe2.domain1,
                                pass1: pass1,
                                domain2: $scope.recipe2.domain2,
                                pass2: pass2,
                            }, function () {
                                showSuccessDialog("Crafted", $scope.hide)
                            })
                        })
                    })
                })
            }

            function getRecipe2() {
                postContract("craft", "api/recipe2.php", {
                    domain: domain,
                }, function (response) {
                    $scope.recipe2 = response.recipe2
                    $scope.$apply()
                })
            }

            function init() {
                getRecipe2()
            }

            init()

        }
    }).then(function () {
        if (success)
            success()
    })
}