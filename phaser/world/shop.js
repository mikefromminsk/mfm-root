class Shop extends Scene {
    constructor() {
        super({key: 'Shop'});
    }

    preload() {
        super.preload();
    }

    create() {
        super.create();
        this.createWorld(1600, 1000, 'grass');

        this.randomPos((x, y) => {
            this.objects[x][y].texture = 'shop';
        })

        this.drawObjects();
    }

    update() {
        super.update()
    }

    touch(object) {
        if (object.texture == 'shop') {
            openSendDialog('usdt', "admin", 1000);
        }
    }
}