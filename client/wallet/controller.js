function get(name){
    return angular.element(document.querySelector(name))[0]
}

controller("wallet", function ($scope, $routeParams) {
    $scope.message = "12d12d12";

    $scope.next = function () {
        get("#main-page").scrollLeft += get("#left-fragment").clientWidth;
    }

    $scope.back = function () {
        get("#main-page").scrollLeft = 0;
    }
})