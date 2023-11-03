function showNewDomain(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/store/domain/index.html',
        controller: function ($scope, $mdBottomSheet) {
            $scope.$watch('search_text', function (newValue) {
                if (newValue == null) return;
                if (newValue == "") {
                    $scope.domains = []
                } else {
                    post('/store/api/search', {
                        path: "/",
                        search_text: newValue
                    }, function (response) {
                        $scope.domains = response.results
                        $scope.$apply()
                    }, function () {
                        $scope.domains = []
                        $scope.$apply()
                    })
                }
            })
            if (DEBUG) {
                $scope.search_text = "da"
            }
            $scope.choose = function (value) {
                $mdBottomSheet.hide(value)
            }
        }
    }).then(function (value) {
        success(value)
    })
}