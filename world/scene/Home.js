class Home extends Base {
    constructor() {
        super({})
        this.isCreativeMode = false;
        this.inHand = null;
    }

    init(scene) {
        this.scene = scene
    }

    reload() {
        postContract("world", "api/scene.php", {scene: this.scene.name}, (scene) => {
            this.scene = scene
            this.loadImage(scene.texture)
            for (let object of scene.objects) {
                this.loadImage(object.domain)
            }
            this.load.on('complete', this.drawObjects, this);
            this.load.start();
        })
    }

    preload() {
        super.preload()
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

    touch(values, x, y) {
        if (values.object != null) {
            if (values.object.domain == 'table') {
                openCraft2("axe", function () {

                })
            } else {
                postContractWithGas("world", "api/move.php", {
                    scene: this.scene.name,
                    x: x,
                    y: y,
                }, () => {
                    this.reload();
                })
            }
        }
        if (values.avatar != null) {
            postContractWithGas("world", "api/move.php", {
                scene: this.scene.name,
                x: x,
                y: y,
            }, () => {
                this.reload();
            })
        }
    }
}