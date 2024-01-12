function openOnboardingDialog(success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/onboarding/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.texts = [{
                title: "Welcome to DataChain Wallet",
                image: "/wallet/img/welcome.svg",
            },{
                title: "For all actions you need Data token",
                image: "/wallet/img/gas-station.svg",
            },{
                title: "Login and get Data tokens",
                image: "/wallet/img/gift-logo.svg",
            },
            ]
            $scope.selectedIndex = 0
            $scope.next = function () {
                if ($scope.selectedIndex != 2) {
                    $scope.selectedIndex += 1;
                } else {
                    openLogin()
                }
            }
            $scope.$watch('selectedIndex', function (newValue, oldValue) {
                if (oldValue == 2) {
                    openLogin()
                }
            })

            function openLogin() {
                $scope.back()
                loginFunction(success)
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}
