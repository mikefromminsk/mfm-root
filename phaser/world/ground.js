class Ground extends Scene {
    constructor() {
        super({key: 'Ground'});
        this.ground = [];
    }

    preload() {
        super.preload();
    }

    create() {
        super.create();
        this.createWorld(1600, 1000, 'grass')
        this.randomGround();
        this.drawGround();
    }

    randomGround() {
        for (let y = 0; y < this.gridHeight; y++) {
            this.ground[y] = [];
            for (let x = 0; x < this.gridWidth; x++)
                if (Phaser.Math.Between(1, 10) == 3) {
                    this.ground[y][x] = Phaser.Math.Between(10, 13);
                }
        }
    }

    drawGround() {
        for (let y = 0; y < this.ground.length; y++) {
            for (let x = 0; x < this.ground[y].length; x++) {
                let textureType = this.ground[y][x];
                if (textureType != null)
                    this.add.sprite(
                        x * this.cellSize,
                        y * this.cellSize,
                        'basic',
                        textureType
                    ).setOrigin(0);
            }
        }
    }
}