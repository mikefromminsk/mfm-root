app.controller('login', function($scope, $http) {
    $scope.email = "x29a100@mail.ru"
    $scope.login_success = false
    $scope.login_error = false
    $scope.login_requesting = false
    $scope.sendEmailCode = function(){
        $scope.login_requesting = true
        $http.post("api/login.php", {email: $scope.email}, config).then(function(){
            $scope.login_success = true
            $scope.login_error = false
            $scope.login_requesting = false
        }, function () {
            $scope.login_success = false
            $scope.login_error = true
            $scope.login_requesting = false
        })
    }
});