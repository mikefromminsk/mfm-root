controller("search", function ($scope) {

    $scope.apps = $dark.store.keys()
    $scope.appIsMine = function (appName) {
        return $dark.domain_key_get(appName) != null
    }

    $scope.searchText = null
    $scope.$watch(function () {
        return $scope.searchText;
    }, function () {
        if ($scope.searchText != null) {
            $dark.similar($scope.searchText, function (domain_similar) {
                $scope.domain_similar = domain_similar;
                $scope.$apply();
            }, function () {
                $scope.domain_similar = null
            })
        } else {
            $scope.domain_similar = store.get("search_history");
        }
    });

    $scope.openApp = function (domain_name) {
        let search_history = store.get("search_history") || []
        while (search_history.length >= 5) search_history.pop();
        search_history.unshift(domain_name);
        store.set("search_history", search_history)
        $scope.open(domain_name)
    }
})