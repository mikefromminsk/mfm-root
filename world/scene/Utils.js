class MessageBus extends Phaser.Events.EventEmitter {
}

const messageBus = new MessageBus();

class Utils extends Phaser.Scene {
    constructor(config) {
        super(config)
    }

    static isClicked = false;

    static click(callback) {
        return function (pointer) {
            if (!Utils.isClicked) {
                Utils.isClicked = true;
                setTimeout(() => {
                    Utils.isClicked = false;
                    callback(pointer)
                }, 100);
            }
        }
    }

    loadItem(texture) {
        if (texture != null)
            this.load.image(texture, 'assets/item/' + texture + '.png')
    }

    loadBlock(texture) {
        if (texture != null)
            this.load.image(texture, 'assets/block/' + texture + '.png')
    }

    loadUI(texture) {
        this.load.image(texture, 'assets/ui/' + texture + '.png')
    }

    loadImage(texture) {
        this.load.image(texture, 'assets/' + texture + '.png')
    }

    loadAvatar(texture) {
        this.load.spritesheet(texture + '64',
            'assets/avatar/' + texture + '.png',
            {frameWidth: 64, frameHeight: 64});
        this.load.spritesheet(texture + '192',
            'assets/avatar/' + texture + '.png',
            {frameWidth: 192, frameHeight: 195});
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

    createSprite(x, y, texture, frame) {
        let sprite = this.physics.add.sprite(x * this.cellSize, y * this.cellSize, texture, frame || 0)
        sprite.setDepth(y * this.cellSize + sprite.height / 2)
        return sprite;
    }
}