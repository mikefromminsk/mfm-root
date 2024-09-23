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
                    callback(pointer)
                    Utils.isClicked = false;
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
}