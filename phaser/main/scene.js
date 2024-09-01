class MainScene extends BaseScene {
    constructor() {
        super({key: 'MainScene'})
    }

    preload() {
        super.preload()
        this.load.image('sky', 'assets/sky.png')
        this.load.image('tree', 'assets/tree.png')
        this.load.image('enemy', 'assets/enemy.png')
        this.load.image('star', 'assets/star.png')
        this.load.image('portal', 'assets/portal.png')
    }

    create() {
        super.create()
        this.add.image(400, 300, 'sky')

        this.anims.create({
            key: 'left',
            frames: this.anims.generateFrameNumbers('dude', {start: 0, end: 3}),
            frameRate: 10,
            repeat: -1
        })

        this.anims.create({
            key: 'turn',
            frames: [{key: 'dude', frame: 4}],
            frameRate: 20
        })

        this.anims.create({
            key: 'right',
            frames: this.anims.generateFrameNumbers('dude', {start: 5, end: 8}),
            frameRate: 10,
            repeat: -1
        })

        this.trees = this.physics.add.staticGroup()

        for (var i = 0; i < 10; i++) {
            var x = Phaser.Math.Between(50, 750)
            var y = Phaser.Math.Between(50, 550)
            var tree = this.trees.create(x, y, 'tree')
            tree.setScale(2)
            tree.refreshBody()
            tree.setDepth(y + 10)
        }

        this.trees.children.iterate((tree) => {
            tree.body.checkCollision.none = true
        })

        this.stars = this.physics.add.group({
            key: 'star',
            repeat: 3,
            setXY: {x: 150, y: 150, stepX: 200}
        })

        this.physics.add.overlap(this.player, this.stars, this.collectstar, null, this)

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
        this.scene.start('NextScene')
    }

    collectstar(player, star) {
        star.disableBody(true, true)
        this.maxSpeed += 50
    }
}