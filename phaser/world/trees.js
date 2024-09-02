class Trees extends Scene {
    constructor() {
        super({key: 'Trees'});
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
        this.objects = this.emptyGrid();
        this.forGrid((x, y) => {
            if (Phaser.Math.Between(1, 3) == 3) {
                this.objects[x][y].texture = 'tree';
            }
        })
    }

    drawObjects() {
        this.forGrid((x, y) => {
            if (this.objects[x][y].texture != null) {
                this.objects[x][y].sprite = this.add.sprite(
                    x * this.cellSize,
                    y * this.cellSize,
                    'basic',
                    this.basic[this.objects[x][y].texture]
                ).setOrigin(0);
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