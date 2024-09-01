function addStore($scope) {

    $scope.openAppSettings = function () {
        openAppSettings($scope.selectedToken, init)
    }

    $scope.openProfile = function (app) {
        if (app.installed) {
            openWeb(location.origin + "/" + app.domain + "?domain=" + $scope.selectedToken, init)
        } else {
            //openProfile(app)
        }
    }

    $scope.installApp = function (app) {
        postContractWithGas("wallet", "store/api/install.php", {
            domain: $scope.selectedToken,
            app_domain: app.domain,
        }, function () {
            showSuccess("Install success")
        })
    }

    function loadApps() {
        postContract("wallet", "store/api/apps.php", {
            domain: $scope.selectedToken,
        }, function (response) {
            $scope.apps = response.apps || {}
            $scope.$apply()
        })
    }

    function init(){
        loadApps()
    }

    init()
}