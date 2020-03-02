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

app.controller('MainController', function ($rootScope, $scope, $mdSidenav, $mdDialog, $location, $routeParams, $http, $q) {

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

    $scope.domain_similar = [];
    $scope.searchText = ""
    $scope.$watch(function () {
        return $scope.searchText;
    }, function () {
        if ($scope.searchText !== "")
            $scope.domain_similar = $dark.similar($scope.searchText)
    });

    $scope.open($location.path().substr(1) || document.querySelector("meta[name='start-page']").getAttribute("content"));
});
