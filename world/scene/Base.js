class Base extends Utils {
    constructor(config) {
        super(config)
        this.maxSpeed = 200
        this.cellSize = 32
        this.touchData = new Map();
    }

    createWorld(gridWidth, gridHeight, texture) {
        let width = gridWidth * this.cellSize
        let height = gridHeight * this.cellSize
        this.physics.world.setBounds(0, 0, width, height)
        this.cameras.main.setBounds(0, 0, width, height)

        this.background = this.add.tileSprite(0, 0, width, height, texture)
        this.background.setOrigin(0, 0)

        this.gridWidth = gridWidth
        this.gridHeight = gridHeight
        this.objects = this.emptyGrid();
    }

    drawObjects() {
        let sprites = []
        this.forGrid((x, y) => {
            var object = this.objects[x][y]
            if (object.texture != null) {
                if (object.sprite != null) {
                    object.sprite.destroy()
                }
                let sprite = this.physics.add.sprite(
                    x * this.cellSize,
                    y * this.cellSize,
                    object.texture
                ).setOrigin(0);
                sprite.setData('object', object)
                object.sprite = sprite
                sprites.push(sprite)
            }
        })
        if (this.objectCollaider != null)
            this.physics.world.removeCollider(this.objectCollaider);
        this.objectCollaider = this.physics.add.overlap(this.player, sprites, this.touchCheck, null, this)
        this.touchGrid = this.emptyGrid();
    }

    touchCheck(player, sprite) {
        const currentTime = new Date().getTime();
        var x = Math.ceil(sprite.x / this.cellSize)
        var y = Math.ceil(sprite.y / this.cellSize)
        var data = this.touchGrid[x][y]
        if (data.startTouch == null) {
            data = {startTouch: 0, lastTouch: 0}
            this.touchGrid[x][y] = data
        }
        if (currentTime - data.lastTouch > 300) {
            data.startTouch = currentTime;
        }
        if (data.startTouch + 300 > currentTime) {
            data.startTouch = 0
            this.touch(sprite.data.values.object, x, y)
        }
        data.lastTouch = currentTime;
    }

    touch(object, x, y) {
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
        this.forGrid(function (x, y) {
            let distanceWithPlayer = Phaser.Math.Distance.Between(
                this.player.x, this.player.y,
                x * this.cellSize, y * this.cellSize
            );
            if (distanceWithPlayer < distance) {
                callback(x, y, distance)
            }
        })
    }

    randomPos(callback) {
        let x = Phaser.Math.Between(2, 2) // this.gridWidth - 1
        let y = Phaser.Math.Between(2, 2) // this.gridHeight - 1
        callback(x, y)
    }


    loadImage(texture) {
        this.load.image(texture, 'assets/' + texture + '.png')
    }

    preload() {
        this.load.spritesheet('avatar', 'assets/avatar.png', {frameWidth: 32, frameHeight: 48})
        this.loadImage("diamond_pickaxe")
    }

    create() {
        this.player = this.physics.add.sprite(15, 30, 'avatar')
        this.player.setCollideWorldBounds(true)
        this.cameras.main.startFollow(this.player)
        this.cameras.main.setZoom(1.5);

        this.anims.create({
            key: 'left',
            frames: this.anims.generateFrameNumbers('avatar', {start: 0, end: 3}),
            frameRate: 10,
            repeat: -1
        })

        this.anims.create({
            key: 'turn',
            frames: [{key: 'avatar', frame: 4}],
            frameRate: 20
        })

        this.anims.create({
            key: 'right',
            frames: this.anims.generateFrameNumbers('avatar', {start: 5, end: 8}),
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
            this.stop()
        })

        this.input.keyboard.addKeys('W,A,S,D')
    }

    stop() {
        this.joystick.style.display = 'none'
        this.stick.style.left = '25px'
        this.stick.style.top = '25px'
        this.player.setVelocity(0)
        this.player.anims.play('turn')
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