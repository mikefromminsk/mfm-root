class UI extends Utils {
    constructor() {
        super({active: true});
    }

    preload() {
        this.loadUI('backpack');
    }

    create() {
        new Button(this, 16 + 32, window.innerHeight - 16 - 32, 'backpack', () => {
            openInventory((domain) => {
                messageBus.emit('select', domain);
            });
        });
    }
}