function openChest(scene, pos, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/chest/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.chestGet = function (domain) {
                postContractWithGas("world", "api/send.php", {
                    from_path: `world/${scene}/blocks/${pos}`,
                    to_path: `world/avatar/${wallet.address()}`,
                    domain: domain,
                    amount: 1,
                }, init)
            }

            $scope.chestPut = function (domain) {
                postContractWithGas("world", "api/send.php", {
                    from_path: `world/avatar/${wallet.address()}`,
                    to_path: `world/${scene}/blocks/${pos}`,
                    domain: domain,
                    amount: 1,
                }, init)
            }

            function avatarInventory(){
                dataObject(`world/avatar/${wallet.address()}/inventory`, function (inventory) {
                    $scope.inventory = inventory
                    $scope.$apply()
                })
            }

            function chestInventory(){
                dataObject(`world/${scene}/blocks/${pos}/inventory`, function (chest) {
                    $scope.chest = chest
                    $scope.$apply()
                })
            }


            function init() {
                avatarInventory()
                chestInventory()
            }

            $scope.mode = ""
            $scope.setMode = function (mode) {
                $scope.mode = mode
                if (mode == "chest") {
                    chestInventory()
                } else if (mode == "inventory") {
                    avatarInventory()
                }
            }
            $scope.setMode("chest")
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}