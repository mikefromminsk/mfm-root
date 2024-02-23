function openProfile(app, success) {
    window.$mdDialog.show({
        templateUrl: '/store/profile/index.html',
        controller: function ($scope) {
            $scope.app = app
            addFormats($scope)
        }
    }).then(function () {
        if (success)
            success()
    })
}