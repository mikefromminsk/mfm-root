let app = angular.module('AngularApp', [
    'ngRoute',
    'ngMaterial',
    'ngAnimate',
]);

app.config(function ($routeProvider, $controllerProvider, $mdThemingProvider) {
    app.register = $controllerProvider.register;
    app.routeProvider = $routeProvider;

    $mdThemingProvider.theme('default')
        .primaryPalette('blue')
        .accentPalette('teal')
        .warnPalette('red')
        .backgroundPalette('grey')
        .dark();
});

function controller(controllerId, callback) {
    app.register(controllerId, callback);
}

function property(propertyName, propertyObject) {
    app.factory(propertyName, function () {
        return propertyObject;
    });
}

function loader(scriptPath) {

    if (document.querySelector('script[src="' + scriptPath + '"]') != null)
        return null;

    return {
        load: function ($q) {
            let result = $q.defer();
            let script = document.createElement("script");
            script.async = "async";
            script.type = "text/javascript";
            script.src = scriptPath;
            script.onload = script.onreadystatechange = function (_, isAbort) {
                if (!script.readyState || /loaded|complete/.test(script.readyState)) {
                    if (isAbort)
                        result.reject();
                    else
                        result.resolve();
                }
            };
            script.onerror = function () {
                result.reject();
            };
            document.querySelector("head").appendChild(script);
            return result.promise;
        }
    };
}

let pathToRootDir = window.location.origin + window.location.pathname
if (pathToRootDir.endsWith("index.html"))
    pathToRootDir = pathToRootDir.substr(0, pathToRootDir.length - "index.html".length)

$dark.init(pathToRootDir);

app.controller('MainController', function ($rootScope, $scope, $mdSidenav, $mdDialog, $location, $routeParams, $http) {

    $scope.open = function (route) {
        let params = route.replace('\\', '/').split('/')
        let appName = params[0]
        let routeTemplate = "";
        for (let i = 1; i < params.length; i++)
            routeTemplate = routeTemplate + "/:arg" + (i - 1);
        if (appName !== "") {
            app.routeProvider.when("/" + appName + routeTemplate, {
                templateUrl: pathToRootDir + appName + "/index.html",
                controller: appName,
                resolve: loader(pathToRootDir + appName + "/controller.js")
            });
            // set global path for all urls
            if (document.getElementsByTagName("base").length === 0)
                document.getElementsByTagName('head')[0].appendChild(document.createElement("base"));
            document.getElementsByTagName("base")[0].href = pathToRootDir + appName + "/"
        }
        document.title = appName
        $location.path(route)
    };

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

    $scope.open($location.path().substr(1) || document.querySelector("meta[name='start-page']").getAttribute("content"));
});