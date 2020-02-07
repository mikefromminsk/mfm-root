controller("login", function ($scope, $http, $routeParams, api_url) {
    $scope.toggleLoginReg = true
    $scope.userToken = $routeParams.arg0

    $scope.message = null
    $scope.login = "selevich@mail.ru"
    $scope.password = "12345678"
    $scope.agreeWithTeems = false

    $scope.login_request_in_progress = false;
    $scope.loginButton = function () {
        $scope.login_request_in_progress = true
        store.clear()
        $http.post(api_url + "login_check.php", {
            user_login: $scope.login,
            user_password: $scope.password,
        })
            .then(function (response) {
                $scope.login_request_in_progress = false
                if (response.data.message == null) {
                    store.set("user_login", $scope.login)
                    store.set("user_session_token", response.data.user_session_token)
                    store.set("user_stock_token", response.data.user_stock_token)
                    $scope.open('DarkWallet')
                } else {
                    $scope.message = response.data.message
                }
            }, function () {
                $scope.login_request_in_progress = false
                alert("Connection failed to " + api_url)
            })
    }

    if ($scope.userToken != null){
        $http.post(api_url + "login_check.php", {
            token: $scope.userToken,
        })
            .then(function (response) {
                if (response.data.message == null) {
                    store.set("user_login", $scope.login)
                    store.set("user_session_token", response.data.user_session_token)
                    store.set("user_stock_token", response.data.user_stock_token)
                    setTimeout(function () {
                        $scope.open('DarkWallet')
                        $scope.$apply();
                    }, 2000)
                } else {
                    $scope.userToken = null
                    $scope.message = "Verification fail. Please login again"
                }
            }, function () {
                alert("Connection failed to " + api_url)
            });

    }
})