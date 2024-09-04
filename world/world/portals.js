class Portal extends Scene {

    preload() {
        super.preload()
        this.load.image('portal', 'assets/demo/portal.png')
    }

    create() {
        super.create()
        super.createWorld(1600, 1000, 'grass')

        var portalX = Phaser.Math.Between(50, 750)
        var portalY = Phaser.Math.Between(50, 550)
        this.portal = this.physics.add.sprite(portalX, portalY, 'portal')
        this.portal.setScale(2)
        this.portal.setDepth(portalY + 10)

        this.physics.add.overlap(this.player, this.portal, this.enterPortal, null, this)
    }

    update() {
        super.update()
    }

    enterPortal(player, portal) {
        this.scene.start('Ground')
    }

}