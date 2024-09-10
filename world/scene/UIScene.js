class UIScene extends Utils {
    constructor() {
        super({active: true});
    }

    preload() {
        this.loadImage('diamond_pickaxe');
        this.loadImage('diamond_sword');
        this.loadImage('plus');
        this.load.spritesheet('buttonSpriteSheet', 'assets/chest16f.png', { frameWidth: 32, frameHeight: 32 });
    }

    create() {
        new Switcher(this, 50, window.innerHeight - 50, function () {
            openInventory(function (domain) {
                messageBus.emit('select', domain);
            });
        }).setScale(2);

        var button = new Button(this, 100, 100, 'buttonSpriteSheet', () => {
            openWorldDeposit(function () {
                button.reset()
            })
        });

    }
}