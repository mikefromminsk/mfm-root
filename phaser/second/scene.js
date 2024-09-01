class NextScene extends BaseScene {
    constructor() {
        super({ key: 'NextScene' })
    }

    preload() {
        super.preload()
        this.load.image('sky', 'assets/sky.png')
    }

    create() {
        super.create()

        // Создайте повторяющийся фон
        this.background = this.add.tileSprite(0, 0, 1600, 1200, 'sky')
        this.background.setOrigin(0, 0)

        // Установите размеры мира
        this.physics.world.setBounds(0, 0, 1600, 1200)

        // Установите размеры камеры
        this.cameras.main.setBounds(0, 0, 1600, 1200)

        // Сделайте так, чтобы камера следовала за игроком
        this.cameras.main.startFollow(this.player)
    }
}