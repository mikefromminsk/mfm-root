class Scene extends Utils {
    constructor(config) {
        super(config);
        this.maxSpeed = 200;
        this.cellSize = 32;
        this.currentScene = null;
        this.disableMoveAnimation = false;
        this.inHand = null;
    }

    preload() {

    }

    init(scene_name) {
        this.scene_name = scene_name;
    }

    create() {
        //subscribe('data', data => console.log(data));
        messageBus.on('select', domain => {
            this.disableUpdates = domain != null;
            this.inHand = domain;
        });
        this.reload()
        setInterval(() => this.sendPos(), 300);
        subscribe('move', (data) => this.receiveEnemyPos(data));
        subscribe('teleport', (data) => this.teleport(data));
    }

    reload() {
        this.disableUpdates = true;
        postContract("world", "api/scene.php", {
            scene: this.scene_name,
            address: wallet.address(),
        }, (response) => {
            this.scene = response.scene;
            this.avatar = response.avatar;
            if (this.avatar.texture == null)
                this.avatar.texture = "base";
            this.load.spritesheet(this.avatar.texture + '64',
                'assets/avatar/' + this.avatar.texture + '.png',
                {frameWidth: 64, frameHeight: 64});
            this.load.spritesheet(this.avatar.texture + '192',
                'assets/avatar/' + this.avatar.texture + '.png',
                {frameWidth: 192, frameHeight: 195});
            this.scene.settings.texture = 'green_concrete'
            this.loadBlock(this.scene.settings.texture);
            for (const object of Object.values(this.scene.blocks || {})) {
                this.loadBlock(object.domain)
            }
            this.load.on('complete', this.createScene, this);
            this.load.start();
        });
    }

    createScene() {
        if (this.currentScene !== this.scene_name) {
            this.currentScene = this.scene_name;
            this.setupScene();
        }
        this.setupJoystick();
        this.setupPlayer();
        this.createBlocks();
        this.updateTouchCollider();

        this.put();
        this.disableUpdates = false;
    }

    setupScene() {
        if (this.background) this.background.destroy();
        let width = this.scene.settings.width * this.cellSize;
        let height = this.scene.settings.height * this.cellSize;
        this.physics.world.setBounds(0, 0, width, height);
        this.cameras.main.setBounds(0, 0, width, height);
        this.background = this.add.tileSprite(0, 0, width, height, this.scene.settings.texture).setOrigin(0, 0);
        this.gridWidth = this.scene.settings.width;
        this.gridHeight = this.scene.settings.height;
    }

    createBlocks() {
        if (this.touchable) this.touchable.forEach(sprite => sprite.destroy());
        this.touchable = [];
        for (const key of Object.keys(this.scene.blocks || {})) {
            var object = this.scene.blocks[key]
            var pos = key.split(':')
            var x = parseInt(pos[0])
            var y = parseInt(pos[1])
            let sprite = this.createSprite(x, y, object.domain);
            sprite.setData('object', object);
            this.touchable.push(sprite);
        }
    }

    addEnemy(avatar) {
        if (avatar.address !== wallet.address()) {
            let sprite = this.createSprite(avatar.x, avatar.y, this.avatar.texture + '64', 3);
            sprite.setData('avatar', avatar);
            this.touchable.push(sprite);
            //this.createAnimation(`avatar_${avatar.address}`, 'avatar');
        }
    }

    updateTouchCollider() {
        if (this.touchCollaider) this.physics.world.removeCollider(this.touchCollaider);
        this.touchCollaider = this.physics.add.overlap(this.player, this.touchable, this.touchCheck, null, this);
        this.touchGrid = this.emptyGrid();
    }

    createSprite(x, y, texture, frame) {
        return this.physics.add.sprite(x * this.cellSize, y * this.cellSize, texture, frame || 0);
    }

    createAnimation(key, texture) {
        let frameCount = this.textures.get(texture).source[0].width / 32;
        this.anims.create({
            key: key,
            frames: this.anims.generateFrameNumbers(texture, {start: 0, end: frameCount - 1}),
            frameRate: 10,
            hideOnComplete: true
        });
    }

    setupPlayer() {
        if (this.player == null) {
            this.player = this.physics.add.sprite(15, 30, this.avatar.texture + '64', (10 - 1) * 18).setCollideWorldBounds(true);
            this.cameras.main.startFollow(this.player);
            this.cameras.main.setZoom(1.5);
            this.createPlayerAnimations();
        }
    }

    createPlayerAnimations() {
        let avatar = this.avatar.texture;
        let anims = this.anims
        anims.create({key: 'turn_left', frames: [{key: avatar + '64', frame: (10 - 1) * 18}], frameRate: 20});
        anims.create({key: 'turn_right', frames: [{key: avatar + '64', frame: (12 - 1) * 18}], frameRate: 20});

        function moveAnim(name, line) {
            let frameInLine = 18;
            let frames = 9;
            anims.create({
                key: name,
                frames: anims.generateFrameNumbers(avatar + '64', {
                    start: (line - 1) * frameInLine,
                    end: (line - 1) * frameInLine + (frames - 1)
                }),
                frameRate: 10,
                repeat: -1
            })
        }

        moveAnim('left', 10);
        moveAnim('right', 12);
        moveAnim('up', 9);
        moveAnim('down', 11);

        function fightAnim(name, line) {
            let frameInLine = 6;
            let frames = 6;
            anims.create({
                key: name,
                frames: anims.generateFrameNumbers(avatar + '192', {
                    start: (line - 1) * frameInLine,
                    end: (line - 1) * frameInLine + (frames - 1)
                }),
                frameRate: 6,
            })
        }

        fightAnim('fight_top', 16);
        fightAnim('fight_left', 17);
        fightAnim('fight_bottom', 18);
        fightAnim('fight_right', 19);

        this.player.on('animationcomplete', function (animation) {
            if (animation.key.startsWith('fight')) {
                this.anims.play('turn_right');
            }
        })
    }

    setupJoystick() {
        this.joystick = document.getElementById('joystick');
        this.stick = this.joystick.querySelector('.stick');
        this.input.on('pointerdown', this.showJoystick.bind(this));
        this.input.on('pointermove', this.moveJoystick.bind(this));
        this.input.on('pointerup', this.hideJoystick.bind(this));
    }

    showJoystick(pointer) {
        this.joystick.style.display = 'block';
        this.joystick.style.left = (pointer.x - 50) + 'px';
        this.joystick.style.top = (pointer.y - 50) + 'px';
        this.joystick.dataset.pointerId = pointer.id;
    }

    moveJoystick(pointer) {
        if (this.joystick.style.display === 'block' && this.joystick.dataset.pointerId == pointer.id) {
            let rect = this.joystick.getBoundingClientRect();
            let x = pointer.x - rect.left - 50;
            let y = pointer.y - rect.top - 50;
            let distance = Math.sqrt(x * x + y * y);
            let maxDistance = 25;
            if (distance > maxDistance) {
                let angle = Math.atan2(y, x);
                x = maxDistance * Math.cos(angle);
                y = maxDistance * Math.sin(angle);
            }
            this.stick.style.left = (25 + x) + 'px';
            this.stick.style.top = (25 + y) + 'px';
        }
    }

    hideJoystick(pointer) {
        if (this.joystick.dataset.pointerId == pointer.id) {
            this.player.setVelocity(0, 0);
            let {deltaX} = this.getJoystickDeltas();
            this.joystick.style.display = 'none';
            if (!this.disableMoveAnimation) {
                this.player.anims.play(deltaX < 0 ? 'turn_left' : 'turn_right');
            }
        }
    }

    update() {
        if (!this.disableUpdates) {
            let {deltaX, deltaY} = this.getJoystickDeltas();
            let distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
            let speedFactor = Math.min(distance / 50, 1);
            let speed = this.maxSpeed * speedFactor;
            this.player.setDepth(this.player.y);
            if (this.joystick.style.display === 'block') {
                let angle = Math.atan2(deltaY, deltaX);
                this.player.setVelocityX(speed * Math.cos(angle));
                this.player.setVelocityY(speed * Math.sin(angle));
                if (!this.disableMoveAnimation) {
                    if (deltaX < -10) this.player.anims.play('left', true);
                    else if (deltaX > 10) this.player.anims.play('right', true);
                    else if (deltaY < -10) this.player.anims.play('up', true);
                    else if (deltaY > 10) this.player.anims.play('down', true);
                }
            }
        }
    }

    sendPos() {
        if (!this.disableUpdates) {
            let speedX = this.player.body.velocity.x
            let speedY = this.player.body.velocity.y
            if (speedX != this.lastSpeedX && speedY != this.lastSpeedY) {
                this.lastSpeedX = speedX;
                this.lastSpeedY = speedY;
                postContract("world", "api/move.php", {
                    scene: this.scene_name,
                    address: wallet.address(),
                    x: this.player.x,
                    y: this.player.y,
                    speedX: speedX,
                    speedY: speedY
                });
            }
        }
    }

    receiveEnemyPos(enemy) {
        if (enemy.address !== wallet.address()) {
            let sprite = this.touchable.find(sprite => sprite.data.get('avatar').address === enemy.address);
            if (sprite) {
                if (enemy.speedX === 0 && enemy.speedY === 0) {
                    sprite.setVelocity(0, 0);
                    sprite.setPosition(enemy.x, enemy.y);
                } else {
                    let targetX = enemy.x + enemy.speedX
                    let targetY = enemy.y + enemy.speedY

                    let deltaX = targetX - sprite.x
                    let deltaY = targetY - sprite.y

                    sprite.setVelocity(deltaX, deltaY);
                }
            } else {
                this.addEnemy(enemy);
                this.updateTouchCollider();
            }
        }
    }


    teleport(data) {
        if (data.address === wallet.address()) {
            /*this.scene_name = data.scene;
            this.reload();*/
        } else {
            let sprite = this.touchable.find(sprite => sprite.data.get('avatar').address === data.address);
            if (sprite) {
                if (data.scene == this.scene_name) {
                    sprite.x = data.x * this.cellSize;
                    sprite.y = data.y * this.cellSize;
                } else {
                    this.touchable = this.touchable.filter(s => s !== sprite);
                    this.updateTouchCollider()
                    sprite.destroy()
                }
            }
        }
    }

    touchCheck(player, sprite) {
        const currentTime = new Date().getTime();
        var x = Math.ceil(sprite.x / this.cellSize);
        var y = Math.ceil(sprite.y / this.cellSize);
        var data = this.touchGrid[x][y];
        if (data.startTouch == null) {
            data.startTouch = 0;
            data.lastTouch = 0;
        }
        if (currentTime - data.lastTouch > 300) {
            data.startTouch = currentTime;
        }
        data.lastTouch = currentTime;
        if (data.startTouch + 300 > currentTime) {
            data.startTouch = 0;

            this.player.anims.stop();

            var animKey = '';
            var rect = this.joystick.getBoundingClientRect();
            var stickRect = this.stick.getBoundingClientRect();
            var deltaX = stickRect.left - rect.left - 25;
            var deltaY = stickRect.top - rect.top - 25;
            if (Math.abs(deltaX) > Math.abs(deltaY)) {
                animKey = deltaX < 0 ? 'fight_left' : 'fight_right'
            } else {
                animKey = deltaY < 0 ? 'fight_top' : 'fight_bottom'
            }
            this.player.anims.play(animKey);
            this.disableMoveAnimation = true;
            setTimeout(() => {
                this.disableMoveAnimation = false;
                this.touch(sprite.data.values, x, y)
            }, 1000);
        }
    }

    touch(values, x, y) {
        if (values.object) {
            if (values.object.domain === 'table') {
                openCraft("axe", () => {
                });
            } else if (values.object.domain === 'chest') {
                // Handle chest interaction
            } else {
                postContractWithGas("world", "api/touch.php", {
                    scene: this.scene_name,
                    pos: x + ':' + y
                }, () => this.reload());
            }
        }
        if (values.avatar) {
            postContract("token", "address.php", {
                domain: wallet.gas_domain,
                address: values.avatar.address,
            }, function (response) {
                postContractWithGas("world", "api/fight.php", {
                    defender_address: values.avatar.address,
                    defender_next_hash: response.next_hash,
                }, (data) => {
                    //console.log(data)
                }, () => {
                    //console.log("error");
                })
            })

        }
    }

    put() {
        this.input.on('pointerdown', Utils.click(pointer => {
                if (this.disableUpdates) {
                    let x = Math.floor(pointer.worldX / this.cellSize);
                    let y = Math.floor(pointer.worldY / this.cellSize);
                    if (this.inHand === 'generator_oak_tree') {
                        for (let i = 0; i < Math.floor(Math.random() * 10) + 10; i++) {
                            let x = Math.floor(Math.random() * 20)//this.gridWidth);
                            let y = Math.floor(Math.random() * 20)// this.gridHeight);
                            if (this.touchGrid[x][y].domain == null) {
                                this.touchGrid[x][y].domain = 'oak_tree'
                                postContractWithGas("world", "api/put_block.php", {
                                    scene: this.scene_name,
                                    domain: 'oak_tree',
                                    pos: x + ':' + y,
                                }, () => console.log('done put oak_tree'));
                                postContractWithGas("world", "api/put_inventory.php", {
                                    scene: this.scene_name,
                                    domain: 'oak_log',
                                    pos: x + ':' + y,
                                    amount: 1,
                                }, () => console.log('done put inventory oak_log'))
                            }
                        }
                        setTimeout(() => this.reload(), 3000);
                    } else {
                        postContractWithGas("world", "api/put_block.php", {
                            scene: this.scene_name,
                            domain: this.inHand,
                            pos: x + ':' + y
                        }, () => this.reload());
                    }
                }
            }
        ))
    }

    getJoystickDeltas() {
        let rect = this.joystick.getBoundingClientRect();
        let stickRect = this.stick.getBoundingClientRect();
        return {
            deltaX: stickRect.left - rect.left - 25,
            deltaY: stickRect.top - rect.top - 25
        };
    }

    emptyGrid() {
        return Array.from({length: this.gridWidth}, () => Array.from({length: this.gridHeight}, () => ({})));
    }
}