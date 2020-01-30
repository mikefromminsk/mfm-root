function get(name){
    return angular.element(document.querySelector(name))[0]
}

controller("wallet", function ($scope, $routeParams) {
    $scope.message = "12d12d12";

    $scope.page = function (index) {
        get("#main-page").scrollLeft = window.innerWidth * index;
    }
})