function openInventory(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/inventory/index.html',
        controller: function ($scope) {
            addFormats($scope)

            dataObject("world/avatar/" + wallet.address() + "/inventory", (inventory) => {
                $scope.inventory = inventory
                $scope.$apply()
            })
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}