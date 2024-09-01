class BaseScene extends Phaser.Scene {
    constructor(config) {
        super(config)
        this.maxSpeed = 200
    }

    preload() {
        this.load.spritesheet('dude', 'assets/dude.png', {frameWidth: 32, frameHeight: 48})
    }

    create() {
        this.player = this.physics.add.sprite(400, 300, 'dude')
        this.player.setCollideWorldBounds(true)

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