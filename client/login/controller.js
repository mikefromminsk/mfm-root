controller("login", function ($scope, $http) {
    $scope.login = "x29a100@mail.ru";
    $scope.password = "";
    $scope.loginButton = function () {
        var url = 'posturl';
        var data = 'parameters';
        $http.post(url, data).then(function (response) {

        }, function (response) {

        });
    }
})