app.controller('actions', function ($scope, $http) {
    $scope.actions = []
    $scope.update_time = null
    $scope.update = function () {
        $http.post("api/actions.php", {}, config).then(function (req) {
            $scope.actions = req.data
            $scope.update_time = new Date()
        }, function () {
        })
    }
    $scope.update()
    setTimeout($scope.update, 3000)
});