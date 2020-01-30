controller("login", function ($scope, $routeParams) {
    $scope.login = "123";
    $scope.password = "123";
    $scope.loginButton = function () {
        $scope.open('wallet');
    }
})