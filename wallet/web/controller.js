function openWeb(link, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/wallet/web/index.html',
        controller: function ($scope, $mdBottomSheet) {
            addFormats($scope)
            $scope.link = link
        }
    }).then(function () {
        if (success)
            success()
    })
}