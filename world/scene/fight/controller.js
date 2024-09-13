function openSelectToken(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/fight/index.html',
        controller: function ($scope) {
            addFormats($scope)

            postContract("wallet", "token/api/tokens.php", {
                address: wallet.address(),
            }, (response) => {
                $scope.activeTokens = response.active
            })
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}