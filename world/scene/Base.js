class Base extends Utils {
    constructor(config) {
        super(config);
        this.maxSpeed = 200; // 106
        this.cellSize = 32;
        this.currentScene = null;
    }

    preload() {
        this.load.spritesheet('avatar64', 'assets/avatar2.png', {frameWidth: 64, frameHeight: 64});
        this.load.spritesheet('avatar192', 'assets/avatar2.png', {frameWidth: 192, frameHeight: 192});
        this.loadImage("diamond_pickaxe");
    }

    createWorld(gridWidth, gridHeight, texture) {
        let width = gridWidth * this.cellSize;
        let height = gridHeight * this.cellSize;
        this.physics.world.setBounds(0, 0, width, height);
        this.cameras.main.setBounds(0, 0, width, height);

        this.background = this.add.tileSprite(0, 0, width, height, texture);
        this.background.setOrigin(0, 0);

        this.gridWidth = gridWidth;
        this.gridHeight = gridHeight;
        this.objects = this.emptyGrid();
    }

    drawObjects() {
        if (this.currentScene !== this.scene.name) {
            this.currentScene = this.scene.name;
            if (this.background) {
                this.background.destroy();
            }
            this.createWorld(this.scene.width, this.scene.height, this.scene.texture);
        }

        if (this.sprites != null)
            this.sprites.forEach((sprite) => {
                sprite.destroy();
            });

        this.sprites = [];

        for (let object of this.scene.objects) {
            this.objects[object.x][object.y] = object;
            let sprite = this.physics.add.sprite(
                object.x * this.cellSize,
                object.y * this.cellSize,
                object.domain,
                0
            ).setOrigin(0);
            sprite.setData('object', object);
            this.sprites.push(sprite);

            // Определяем количество кадров в анимации
            let texture = this.textures.get(object.domain);
            let frameCount = texture.source[0].width / 32;

            // Создаем анимацию для объекта
            this.anims.create({
                key: `object_${object.domain}`,
                frames: this.anims.generateFrameNumbers(object.domain, {start: 0, end: frameCount - 1}),
                frameRate: 10,
                hideOnComplete: true
            });
        }

        for (let avatar of this.scene.avatars) {
            if (avatar.address == wallet.address()) continue;
            let sprite = this.physics.add.sprite(
                avatar.x * this.cellSize,
                avatar.y * this.cellSize,
                'avatar',
                0
            ).setOrigin(0);
            sprite.setData('avatar', avatar);
            this.sprites.push(sprite);

            // Определяем количество кадров в анимации
            let texture = this.textures.get('avatar');
            let frameCount = texture.source[0].width / 32;

            // Создаем анимацию для аватара
            /*this.anims.create({
                key: `avatar_${avatar.address}`,
                frames: this.anims.generateFrameNumbers('avatar', {start: 0, end: frameCount - 1}),
                frameRate: 10,
                hideOnComplete: true
            });*/
        }

        if (this.objectCollaider != null) {
            this.physics.world.removeCollider(this.objectCollaider);
        }
        this.objectCollaider = this.physics.add.overlap(this.player, this.sprites, this.touchCheck, null, this);

        this.touchGrid = this.emptyGrid();
    }

    touchCheck(player, sprite) {
        const currentTime = new Date().getTime();
        var x = Math.ceil(sprite.x / this.cellSize);
        var y = Math.ceil(sprite.y / this.cellSize);
        var data = this.touchGrid[x][y];
        if (data.startTouch == null) {
            data = {startTouch: 0, lastTouch: 0};
            this.touchGrid[x][y] = data;
        }
        if (currentTime - data.lastTouch > 300) {
            data.startTouch = currentTime;
        }
        if (data.startTouch + 300 > currentTime) {
            data.startTouch = 0;

            // Проигрываем анимацию при касании
            if (sprite.data.get('object')) {
                sprite.anims.play(`object_${sprite.data.get('object').domain}`);
            } else if (sprite.data.get('avatar')) {
                sprite.anims.play(`avatar_${sprite.data.get('avatar').address}`);
            }

            // Обработчик завершения анимации
            sprite.on('animationcomplete', () => {
                this.touch(sprite.data.values, x, y);
            });
        }
        data.lastTouch = currentTime;
    }

    touch(object, x, y) {
        // Реализация метода touch
    }

    emptyGrid() {
        let grid = [];
        for (let x = 0; x < this.gridWidth; x++) {
            grid[x] = [];
            for (let y = 0; y < this.gridHeight; y++) {
                grid[x][y] = {};
            }
        }
        return grid;
    }

    forGrid(callback) {
        for (let x = 0; x < this.gridWidth; x++) {
            for (let y = 0; y < this.gridHeight; y++) {
                callback(x, y);
            }
        }
    }

    loadImage(domain) {
        this.load.spritesheet(domain, Base.convertDomainToTextureUrl(domain), {frameWidth: 32, frameHeight: 32});
    }

    static convertDomainToTexture(domain) {
        switch (domain) {
            case 'stone':
                return 'masonry_andesite';
            case 'axe':
                return 'diamond_pickaxe';
            case 'sword':
                return 'diamond_sword';
            case 'table':
                return 'utility_crafting_table';
        }
        return domain;
    }

    static convertDomainToTextureUrl(domain) {
        return "/world/assets/" + Base.convertDomainToTexture(domain) + '.png';
    }

    create() {
        this.player = this.physics.add.sprite(15, 30, 'avatar64');
        this.player.setCollideWorldBounds(true);
        this.cameras.main.startFollow(this.player);
        this.cameras.main.setZoom(1.5);

        this.anims.create({
            key: 'left',
            frames: this.anims.generateFrameNumbers('avatar64', {start: (10 - 1) * 18, end: (10 - 1) * 18 + (9 - 1)}),
            frameRate: 10,
            repeat: -1
        });

        this.anims.create({
            key: 'turn_left',
            frames: [{key: 'avatar64', frame: (10 - 1) * 18}],
            frameRate: 20
        });

        this.anims.create({
            key: 'turn_right',
            frames: [{key: 'avatar64', frame: (12 - 1) * 18}],
            frameRate: 20
        });

        this.anims.create({
            key: 'right',
            frames: this.anims.generateFrameNumbers('avatar64', {start: (12 - 1) * 18, end: (12 - 1) * 18 + (9 - 1)}),
            frameRate: 10,
            repeat: -1
        });

        this.anims.create({
            key: 'fight',
            frames: this.anims.generateFrameNumbers('avatar192', {start: (16 - 1) * 6, end: (16 - 1) * 6 + (6 - 1)}),
            frameRate: 10,
            repeat: -1
        });

        this.anims.create({
            key: 'up',
            frames: this.anims.generateFrameNumbers('avatar64', {start: (9 - 1) * 18, end: (9 - 1) * 18 + (9 - 1)}),
            frameRate: 10,
            repeat: -1
        });

        this.anims.create({
            key: 'down',
            frames: this.anims.generateFrameNumbers('avatar64', {start: (11 - 1) * 18, end: (11 - 1) * 18 + (9 - 1)}),
            frameRate: 10,
            repeat: -1
        });

        this.joystick = document.getElementById('joystick');
        this.stick = this.joystick.querySelector('.stick');

        this.input.on('pointerdown', (pointer) => {
            this.joystick.style.display = 'block';
            this.joystick.style.left = (pointer.x - 50) + 'px';
            this.joystick.style.top = (pointer.y - 50) + 'px';
            this.joystick.dataset.pointerId = pointer.id;
        });

        this.input.on('pointermove', (pointer) => {
            if (this.joystick.style.display === 'block' && this.joystick.dataset.pointerId == pointer.id) {
                var rect = this.joystick.getBoundingClientRect();
                var x = pointer.x - rect.left - 50;
                var y = pointer.y - rect.top - 50;
                var distance = Math.sqrt(x * x + y * y);
                var maxDistance = 25; // Радиус окружности

                if (distance > maxDistance) {
                    var angle = Math.atan2(y, x);
                    x = maxDistance * Math.cos(angle);
                    y = maxDistance * Math.sin(angle);
                }

                this.stick.style.left = (25 + x) + 'px';
                this.stick.style.top = (25 + y) + 'px';
            }
        });

        this.input.on('pointerup', (pointer) => {
            if (this.joystick.dataset.pointerId == pointer.id) {

                // Остановка движения
                this.player.setVelocity(0, 0);

                var rect = this.joystick.getBoundingClientRect();
                var stickRect = this.stick.getBoundingClientRect();
                var deltaX = stickRect.left - rect.left - 25;

                if (deltaX < 0) {
                    this.player.anims.play('turn_left');
                } else {
                    this.player.anims.play('turn_right');
                }
                this.joystick.style.display = 'none';
            }
        });
    }

    update() {
        var rect = this.joystick.getBoundingClientRect();
        var stickRect = this.stick.getBoundingClientRect();
        var deltaX = stickRect.left - rect.left - 25;
        var deltaY = stickRect.top - rect.top - 25;
        var distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
        var maxDistance = 50;
        var speedFactor = Math.min(distance / maxDistance, 1);
        var speed = this.maxSpeed * speedFactor;

        if (this.joystick.style.display === 'block') {
            var angle = Math.atan2(deltaY, deltaX);
            this.player.setVelocityX(speed * Math.cos(angle));
            this.player.setVelocityY(speed * Math.sin(angle));

            if (deltaX < -10) {
                this.player.anims.play('left', true);
            } else if (deltaX > 10) {
                this.player.anims.play('right', true);
            } else if (deltaY < -10) {
                this.player.anims.play('up', true);
            } else if (deltaY > 10) {
                this.player.anims.play('down', true);
            }
        }
        this.player.setDepth(this.player.y);
    }
}