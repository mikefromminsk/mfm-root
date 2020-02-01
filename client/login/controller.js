controller("login", function ($scope, $http, host_url) {
    $scope.message = null;
    $scope.login = "x29a100@mail.ru";
    $scope.password = "12345672";
    $scope.loginButton = function () {
        $http.post(host_url + "login_token.php", {
            user_login: $scope.login,
            user_password: $scope.password,
        })
            .then(function (response) {
                if (response.data.message == null) {
                    $scope.open('DarkWallet/' + response.data.token);
                } else {
                    $scope.message = response.data.message;
                }
            });
    }
})