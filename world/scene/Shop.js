class Shop extends Base {
    constructor() {
        super({key: 'Shop'})
        this.isCreativeMode = false;
        this.inHand = null;
    }

    init(scene) {
        this.scene = scene
    }

    convertTokenToTexture(token) {
        switch (token) {
            case 'wood':
                return 'log_oak';
            case 'stone':
                return 'masonry_andesite';
            case 'axe':
                return 'diamond_pickaxe';
            case 'sword':
                return 'diamond_sword';
            case 'table':
                return 'utility_crafting_table';
        }
        return token
    }

    reload() {
        postContract("world", "api/scene.php", {scene: this.scene.name}, (scene) => {
            this.scene = scene
            this.createWorld(this.scene.width, this.scene.height, this.scene.texture)
            for (let object of this.scene.objects) {
                this.objects[object.x][object.y].texture = this.convertTokenToTexture(object.texture)
            }
            this.drawObjects()
        })
    }

    preload() {
        super.preload()
        this.loadImage('green_concrete_powder')
        this.loadImage('utility_crafting_table')
        this.loadImage('masonry_andesite')
        this.loadImage('log_oak')
    }

    create() {
        super.create()
        this.reload()

        messageBus.on('select', (domain) => {
            this.isCreativeMode = domain != null;
            this.inHand = domain;
        });

        this.input.on('pointerdown', Utils.click((pointer) => {
            if (this.isCreativeMode) {
                const x = Math.floor(pointer.worldX / this.cellSize);
                const y = Math.floor(pointer.worldY / this.cellSize);
                getPin((pin) => {
                    wallet.calcPass(this.inHand, pin, (pass) => {
                        postContractWithGas("world", "api/object_insert.php", {
                            scene: this.scene.name,
                            domain: this.inHand,
                            pass: pass,
                            x: x,
                            y: y,
                        }, () => {
                            showSuccess("Object created")
                            this.reload();
                        })
                    })
                })
            }
        }))
    }

    update() {
        if (!this.isCreativeMode) {
            super.update();
        }
    }

    touch(object, x, y) {
        console.log(22)
        if (object.texture == 'utility_crafting_table') {
            openCraft2("axe", function () {

            })
        }
        if (object.texture == 'log_oak' || object.texture == 'masonry_andesite') {
            //this.stop()
            console.log(x + ' ' + y)
            postContractWithGas("world", "api/object_delete.php", {
                scene: this.scene.name,
                x: x,
                y: y,
            }, () => {
                showSuccess("Object deleted")
                this.reload();
            })
        }
    }
}