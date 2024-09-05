class Switcher extends Phaser.GameObjects.Container {
    constructor(scene, x, y, onSwitch) {
        super(scene, x, y);
        this.scene = scene;
        this.isEnable = false;
        this.onSwitch = onSwitch;

        // Создаем изображения
        this.pickaxeImage = this.scene.add.image(0, 0, 'diamond_pickaxe').setOrigin(0.5);
        this.swordImage = this.scene.add.image(0, 0, 'diamond_sword').setOrigin(0.5).setVisible(false);

        // Добавляем изображения в контейнер
        this.add(this.pickaxeImage);
        this.add(this.swordImage);

        // Делаем контейнер интерактивным
        this.setSize(this.pickaxeImage.width, this.pickaxeImage.height);
        this.setInteractive();

        // Добавляем обработчик событий
        this.on('pointerdown', Utils.click((pointer) => {
            this.isEnable = !this.isEnable;
            this.pickaxeImage.setVisible(!this.isEnable);
            this.swordImage.setVisible(this.isEnable);
            this.onSwitch(this.isEnable);
        }));

        // Добавляем контейнер на сцену
        this.scene.add.existing(this);
    }
}