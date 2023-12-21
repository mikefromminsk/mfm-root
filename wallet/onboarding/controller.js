function openOnboardingDialog(success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/onboarding/index.html",
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.selectedIndex = 0
            $scope.next = function () {
                if ($scope.selectedIndex != 2) {
                    $scope.selectedIndex += 1;
                } else {
                    $scope.back()
                    loginFunction(success)
                }
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}
