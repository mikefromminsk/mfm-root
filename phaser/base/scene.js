class Scene extends Phaser.Scene {
    constructor(config) {
        super(config)
        this.maxSpeed = 200
        this.cellSize = 16
    }

    preload() {
        this.load.image('sky', 'assets/demo/sky.png')
        this.load.spritesheet('dude', 'assets/demo/dude.png', {frameWidth: 32, frameHeight: 48})
        this.load.spritesheet('basic', 'assets/basic/basictiles.png', {
            frameWidth: 16,
            frameHeight: 16
        });
        this.basic = {
            grass: 11,
            flowers: 12,
            water: 13,
            tree: 76,
        }
    }

    createWorld(width, height, texture) {
        this.worldWidth = width
        this.worldHeight = height
        this.gridWidth = Math.ceil(width / this.cellSize)
        this.gridHeight = Math.ceil(height / this.cellSize)
        this.physics.world.setBounds(0, 0, width, height)
        this.cameras.main.setBounds(0, 0, width, height)

        this.background = this.add.tileSprite(0, 0, this.worldWidth, this.worldHeight, 'basic', this.basic[texture])
        this.background.setOrigin(0, 0)
    }

    emptyGrid() {
        let grid = []
        for (let x = 0; x < this.gridWidth; x++) {
            grid[x] = [];
            for (let y = 0; y < this.gridHeight; y++) {
                grid[x][y] = {};
            }
        }
        return grid
    }

    forGrid(callback) {
        for (let x = 0; x < this.gridWidth; x++) {
            for (let y = 0; y < this.gridHeight; y++) {
                callback(x, y)
            }
        }
    }

    forNear(distance, callback) {
        for (let x = 0; x < this.gridWidth; x++) {
            for (let y = 0; y < this.gridHeight; y++) {
                let distanceWithPlayer = Phaser.Math.Distance.Between(
                    this.player.x, this.player.y,
                    x * this.cellSize, y * this.cellSize
                );
                if (distanceWithPlayer < distance) {
                    callback(x, y, distance)
                }
            }
        }
    }

    create() {
        this.player = this.physics.add.sprite(200, 200, 'dude')
        this.player.setCollideWorldBounds(true)
        this.cameras.main.startFollow(this.player)
        this.cameras.main.setZoom(2);

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

        this.joystick = document.getElementById('joystick')
        this.stick = this.joystick.querySelector('.stick')

        this.input.on('pointerdown', (pointer) => {
            this.joystick.style.display = 'block'
            this.joystick.style.left = (pointer.x - 50) + 'px'
            this.joystick.style.top = (pointer.y - 50) + 'px'
            this.joystick.dataset.pointerId = pointer.id
        })

        this.input.on('pointermove', (pointer) => {
            if (this.joystick.style.display === 'block' && this.joystick.dataset.pointerId == pointer.id) {
                var rect = this.joystick.getBoundingClientRect()
                var x = pointer.x - rect.left - 50
                var y = pointer.y - rect.top - 50
                this.stick.style.left = Math.max(0, Math.min(50, x)) + 'px'
                this.stick.style.top = Math.max(0, Math.min(50, y)) + 'px'
            }
        })

        this.input.on('pointerup', (pointer) => {
            if (this.joystick.dataset.pointerId == pointer.id) {
                this.joystick.style.display = 'none'
                this.stick.style.left = '25px'
                this.stick.style.top = '25px'
                this.player.setVelocity(0)
                this.player.anims.play('turn')
            }
        })

        this.joystick.addEventListener('pointermove', (event) => {
            var rect = this.joystick.getBoundingClientRect()
            var x = event.clientX - rect.left - 50
            var y = event.clientY - rect.top - 50
            this.stick.style.left = Math.max(0, Math.min(50, x)) + 'px'
            this.stick.style.top = Math.max(0, Math.min(50, y)) + 'px'
        })

        this.joystick.addEventListener('pointerup', () => {
            this.joystick.style.display = 'none'
            this.stick.style.left = '25px'
            this.stick.style.top = '25px'
            this.player.setVelocity(0)
            this.player.anims.play('turn')
        })

        this.input.keyboard.addKeys('W,A,S,D')
    }

    update() {
        var rect = this.joystick.getBoundingClientRect()
        var stickRect = this.stick.getBoundingClientRect()
        var deltaX = stickRect.left - rect.left - 25
        var deltaY = stickRect.top - rect.top - 25
        var distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY)
        var maxDistance = 50
        var speedFactor = Math.min(distance / maxDistance, 1)
        var speed = this.maxSpeed * speedFactor

        if (this.joystick.style.display === 'block') {
            var angle = Math.atan2(deltaY, deltaX)
            this.player.setVelocityX(speed * Math.cos(angle))
            this.player.setVelocityY(speed * Math.sin(angle))

            if (deltaX < -10) {
                this.player.anims.play('left', true)
            } else if (deltaX > 10) {
                this.player.anims.play('right', true)
            } else {
                this.player.anims.play('turn')
            }
        } else {
            this.player.setVelocity(0)
            this.player.anims.play('turn')

            var velocityX = 0
            var velocityY = 0

            if (this.input.keyboard.keys[65].isDown) { // A
                velocityX = -1
            } else if (this.input.keyboard.keys[68].isDown) { // D
                velocityX = 1
            }

            if (this.input.keyboard.keys[87].isDown) { // W
                velocityY = -1
            } else if (this.input.keyboard.keys[83].isDown) { // S
                velocityY = 1
            }

            var magnitude = Math.sqrt(velocityX * velocityX + velocityY * velocityY)
            if (magnitude > 0) {
                velocityX = (velocityX / magnitude) * speed
                velocityY = (velocityY / magnitude) * speed
            }

            this.player.setVelocityX(velocityX)
            this.player.setVelocityY(velocityY)

            if (velocityX < 0) {
                this.player.anims.play('left', true)
            } else if (velocityX > 0) {
                this.player.anims.play('right', true)
            } else {
                this.player.anims.play('turn')
            }
        }
        this.player.setDepth(this.player.y)
    }
}