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
        postContract("world", "api/scene.php", {scene: scene}, function (data) {
            game.scene.add('Shop', Shop)
            game.scene.add('UIScene', UIScene)
            game.scene.start('Shop', data)
        }, function () {
            openCreateScene(function (scene) {
                loadScene(scene)
            })
        })
    }

    if (wallet.address() == "") {
        openLogin(function () {
            loadScene("home")
        })
    } else {
        loadScene("home")
    }
}