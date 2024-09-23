class Button extends Phaser.GameObjects.Container {
    constructor(scene, x, y, spriteKey, onComplete) {
        super(scene, x, y);
        this.scene = scene;
        this.onComplete = onComplete;

        // Создаем изображение кнопки
        this.buttonImage = this.scene.add.image(0, 0, spriteKey);
        this.add(this.buttonImage);

        // Делаем контейнер интерактивным
        this.setSize(this.buttonImage.width, this.buttonImage.height);
        this.setInteractive();

        // Добавляем обработчик событий
        this.on('pointerdown', Utils.click(() => {
            this.onComplete();
        }));

        // Добавляем контейнер на сцену
        this.scene.add.existing(this);
    }
}