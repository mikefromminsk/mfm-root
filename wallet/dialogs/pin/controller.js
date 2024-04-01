function openPin(domain, success, cancel) {
    if (domain != null && storage.getString(storageKeys.hasPin) == "") {
        if (success)
            success()
    } else if (window.tempPin != null) {
        if (success)
            success(window.tempPin)
    } else {
        window.$mdBottomSheet.show({
            templateUrl: "/wallet/dialogs/pin/index.html",
            controller: function ($scope) {
                addFormats($scope)
                $scope.setMode = domain == null
                $scope.domain = $scope.setMode ? "Set pin" : domain
                $scope.pin = ""

                $scope.add = function (symbol) {
                    $scope.pin += symbol
                    if ($scope.pin.length == 4) {
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
}