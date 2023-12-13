function openWeb(link, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/web/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.link = link
            $scope.back = function () {
                $mdBottomSheet.hide()
            }
        }
    }).then(function () {
        if (success)
            success()
    })
}