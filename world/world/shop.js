class Shop extends Scene {
    constructor() {
        super({key: 'Shop'});
    }

    preload() {
        super.preload();
        this.loadBlock('green_concrete_powder')
        this.loadBlock('furnace_front_on')
    }

    create() {
        super.create();
        this.createWorld(1600, 1000, 'green_concrete_powder');

        this.randomPos((x, y) => {
            this.objects[x][y].texture = 'furnace_front_on';
        })

        this.drawObjects();
    }

    update() {
        super.update()
    }

    touch(object) {
        if (object.texture == 'furnace_front_on') {
            openConstruct(function () {

            });
        }
    }
}