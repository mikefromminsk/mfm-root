function openChest(scene, pos, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/chest/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.chestGet = function (domain) {
                postContractWithGas("world", "api/chest_get.php", {
                    scene: scene,
                    pos: pos,
                    domain: domain,
                }, init)
            }

            $scope.chestPut = function (domain) {
                postContractWithGas("world", "api/chest_put.php", {
                    scene: scene,
                    pos: pos,
                    domain: domain,
                }, init)
            }

            function avatarInventory(){
                postContract("world", "api/inventory.php", {
                    address: wallet.address(),
                }, function (response) {
                    $scope.inventory = response.inventory
                    $scope.$apply()
                })
            }

            function chestInventory(){
                postContract("world", "api/chest.php", {
                    scene: scene,
                    pos: pos,
                }, function (response) {
                    $scope.chest = response.chest
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