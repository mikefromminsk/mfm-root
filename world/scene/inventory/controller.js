function openInventory(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/inventory/index.html',
        controller: function ($scope) {
            addFormats($scope)

            postContract("world", "api/inventory.php", {
                address: wallet.address(),
            }, (response) => {
                $scope.inventory = response.inventory
            })
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}