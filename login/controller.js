controller("login", function ($scope) {

    $scope.token = store.get("user_session_token");
    $scope.login = function (delay) {
        setTimeout(function () {
            $mdDialog.show({
                templateUrl: pathToRootDir + 'darkgui/login.html',
                scope: $scope.$new(),
                controller: function ($scope, $mdDialog) {
                    $scope.toggleLoginReg = true

                    $scope.login = "x29a100@mail.ru"
                    $scope.password = "123123123"

                    $scope.login_message = null
                    $scope.login_in_progress = false;
                    $scope.loginButton = function () {
                        $scope.login_in_progress = true
                        store.clear()
                        $dark.file_get($scope.login, null, function (data) {
                            $scope.login_in_progress = false
                            data = $dark.decode(data, $scope.password);
                            try {
                                let user = JSON.parse(data);
                                store.set("user_email", user.email)
                                $mdDialog.hide();
                            } catch (e) {
                                $scope.login_message = "password error"
                            }
                        }, function () {
                            $scope.login_in_progress = false
                            $scope.login_message = "user dosent exist"
                        })
                    }

                    $scope.agreeWithTeems = false
                    $scope.registration_message = null
                    $scope.registration_in_progress = false
                    $scope.registrationButton = function () {
                        $scope.registration_in_progress = true
                        store.clear()
                        let user = $dark.encode(JSON.stringify({email: $scope.login}), $scope.password);
                        $dark.file_put($scope.login, null, $scope.password, user, function () {
                            $scope.registration_in_progress = false
                            store.set("user_email", user.email)
                            $mdDialog.hide();
                        }, function (message) {
                            $scope.registration_in_progress = false
                            $scope.registration_message = message
                        })
                    }
                },
            }).then(function (answer) {
                $scope.status = 'You said the information was "' + answer + '".';
            });
        }, delay)
    };

    if ($scope.token == null)
        $scope.login(1000);
})