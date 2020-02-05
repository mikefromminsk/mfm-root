controller("login", function ($scope, $http, api_url) {
    $scope.toggleLoginReg = true;

    $scope.message = null;
    $scope.login = "selevich@mail.ru";
    $scope.password = "12345678";
    $scope.agreeWithTeems = false;

    $scope.loginButton = function () {
        store.clear();
        $http.post(api_url + "login_check.php", {
            user_login: $scope.login,
            user_password: $scope.password,
        })
            .then(function (response) {
                if (response.data.message == null) {
                    store.set("user_login", $scope.login);
                    store.set("user_session_token", response.data.user_session_token);
                    store.set("user_stock_token", response.data.user_stock_token);
                    $scope.open('DarkWallet');
                } else {
                    $scope.message = response.data.message;
                }
            }, function () {
                    alert("Connection failed to " + api_url);
            });
    }
})