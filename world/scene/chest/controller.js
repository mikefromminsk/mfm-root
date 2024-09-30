function openChest(scene, pos, success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/chest/index.html',
        controller: function ($scope) {
            addFormats($scope)

            $scope.chestGet = function (domain) {
                postContractWithGas("world", "api/send.php", {
                    from_path: scene + `/blocks/` + pos,
                    to_path: `avatar/` + wallet.address(),
                    domain: domain,
                    amount: 1,
                }, init)
            }

            $scope.chestPut = function (domain) {
                postContractWithGas("world", "api/send.php", {
                    from_path: `avatar/` + wallet.address(),
                    to_path: scene + `/blocks/` + pos,
                    domain: domain,
                    amount: 1,
                }, init)
            }

            function avatarInventory(){
                postContract("world", "api/inventory.php", {
                    path: `avatar/` + wallet.address()
                }, function (response) {
                    $scope.inventory = response.inventory
                    $scope.$apply()
                })
            }

            function chestInventory(){
                postContract("world", "api/inventory.php", {
                    path: scene + `/blocks/` + pos
                }, function (response) {
                    $scope.chest = response.inventory
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