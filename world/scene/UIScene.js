class UIScene extends Utils {
    constructor() {
        super({active: true});
    }

    preload() {
        this.loadImage('diamond_pickaxe');
        this.loadImage('diamond_sword');
    }

    create() {
        new Switcher(this, 50, window.innerHeight - 50, function () {
            openSelectToken(function (domain) {
                messageBus.emit('select', domain);
            });
        }).setScale(2);
    }
}