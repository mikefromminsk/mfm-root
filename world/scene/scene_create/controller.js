function openCreateScene(success) {
    window.$mdBottomSheet.show({
        templateUrl: '/world/scene/scene_create/index.html',
        controller: function ($scope) {
            addFormats($scope)
            $scope.create = function () {
                postContractWithGas("world", "api/scene_insert.php", {
                    scene: $scope.scene,
                    width: $scope.width,
                    height: $scope.height,
                    texture: $scope.texture
                }, function () {
                    $scope.back($scope.scene)
                })
            }
        }
    }).then(function (scene) {
        if (success)
            success(scene)
    })
}