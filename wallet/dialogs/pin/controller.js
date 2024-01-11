function openPin(domain, success) {
    window.$mdBottomSheet.show({
        templateUrl: "/wallet/dialogs/pin/index.html",
        controller: function ($scope) {
            addFormats($scope)
            $scope.domain = domain
            $scope.pin = ""
            $scope.add = function (symbol) {
                $scope.pin += symbol
                if ($scope.pin.length == 6) {
                    window.$mdBottomSheet.hide($scope.pin)
                }
            }
            $scope.remove = function () {
                if ($scope.pin.length > 0)
                    $scope.pin = $scope.pin.substring(0, $scope.pin.length - 1);
            }
        }
    }).then(function (result) {
        if (success)
            success(result)
    })
}