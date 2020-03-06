let angularApplication = angular.module('AngularApp', [
    'ngRoute',
    'ngMaterial',
    'ngAnimate',
]);

angularApplication.config(function ($routeProvider, $controllerProvider, $mdThemingProvider) {
    angularApplication.register = $controllerProvider.register;
    angularApplication.routeProvider = $routeProvider;

    $mdThemingProvider.theme('default')
        .primaryPalette('blue')
        .accentPalette('teal')
        .warnPalette('red')
        .backgroundPalette('grey')
        .dark();
});

function controller(controllerId, callback) {
    angularApplication.register(controllerId, callback);
}

function property(propertyName, propertyObject) {
    angularApplication.factory(propertyName, function () {
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

angularApplication.controller('MainController', function ($rootScope, $scope, $mdSidenav, $mdDialog, $location, $routeParams, $http, $q) {

    $scope.apps = $dark.store.keys()

    $scope.open = function (route) {
        if (route[0] === '/') route = route.substr(1)
        let params = route.replace('\\', '/').split('/')
        let appName = params[0]
        let routeTemplate = "";
        for (let i = 1; i < params.length; i++)
            routeTemplate = routeTemplate + "/:arg" + (i - 1);
        if (appName !== "") {
            angularApplication.routeProvider.when("/" + appName + routeTemplate, {
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

    $scope.open($location.path().substr(1) || document.querySelector("meta[name='start-page']").getAttribute("content"));
});
