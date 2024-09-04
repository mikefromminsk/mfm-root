class Stones extends Scene {
    constructor() {
        super({key: 'Stones'});
    }

    preload() {
        super.preload();
    }

    create() {
        super.create();
        this.createWorld(1600, 1000, 'grass');
        this.randomObjects();
        this.drawObjects();
    }

    randomObjects() {
        this.forGrid((x, y) => {
            if (Phaser.Math.Between(1, 3) == 3) {
                this.objects[x][y].texture = 'stone';
            } else if (Phaser.Math.Between(1, 10) == 3) {
                this.objects[x][y].texture = 'tree';
            }
        })
    }

    update() {
        super.update()

        this.forNear(20, (x, y) => {
            if (this.objects[x][y].texture == 'tree') {
                this.objects[x][y].sprite.destroy()
            }
        });
    }
}