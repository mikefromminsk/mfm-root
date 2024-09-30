function main($scope, $mdBottomSheet, $mdDialog, $mdToast) {
    addFormats($scope)
    window.$mdToast = $mdToast
    window.$mdBottomSheet = $mdBottomSheet
    window.$mdDialog = $mdDialog

    var config = {
        type: Phaser.AUTO,
        width: window.innerWidth,
        height: window.innerHeight,
        physics: {
            default: 'arcade',
            arcade: {
                gravity: {y: 0},
                debug: false
            }
        },
    }

    var game = new Phaser.Game(config)

    function loadScene(scene) {
        postContractWithGas("world", "api/scene_insert.php", {
            scene: scene,
            width: 100,
            height: 100
        }, function () {
            game.scene.add('Scene', Scene)
            game.scene.add('UIScene', UI)
            game.scene.start('Scene', scene)
        })
    }

    var startScene = "home"
    if (wallet.address() == "") {
        openLogin(function () {
            loadScene(startScene)
        })
    } else {
        loadScene(startScene)
    }

    connectWs();
}