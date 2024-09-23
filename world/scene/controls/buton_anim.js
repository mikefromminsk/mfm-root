class ButtonAnim extends Phaser.GameObjects.Container {
    constructor(scene, x, y, spriteSheetKey, onComplete) {
        super(scene, x, y);
        this.scene = scene;
        this.spriteSheetKey = spriteSheetKey;
        this.onComplete = onComplete;

        // Создаем изображение обычного состояния (первый кадр спрайтшита)
        this.normalImage = this.scene.add.image(0, 0, spriteSheetKey, 0)
        this.add(this.normalImage);

        // Делаем контейнер интерактивным
        this.setSize(this.normalImage.width, this.normalImage.height);
        this.setInteractive();

        // Добавляем обработчик событий
        this.on('pointerdown', Utils.click(() => {
            this.playAnimation();
        }));

        // Добавляем контейнер на сцену
        this.scene.add.existing(this);
    }

    playAnimation() {
        // Скрываем обычное изображение
        this.normalImage.setVisible(false);

        // Создаем спрайт для анимации
        this.animationSprite = this.scene.add.sprite(0, 0, this.spriteSheetKey)
        this.add(this.animationSprite);

        // Создаем анимацию
        this.scene.anims.create({
            key: 'buttonAnimation',
            frames: this.scene.anims.generateFrameNumbers(this.spriteSheetKey, { start: 0, end: 15 }),
            frameRate: 20,
            hideOnComplete: false
        });

        // Проигрываем анимацию
        this.animationSprite.play('buttonAnimation');

        // Обработчик завершения анимации
        this.animationSprite.on('animationcomplete', () => {
            this.onComplete();
        });
    }

    reset() {
        if (this.animationSprite) {
            this.animationSprite.destroy();
        }
        this.normalImage.setVisible(true);
    }
}