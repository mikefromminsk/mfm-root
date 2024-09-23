<?php

$input_folder = 'block_bank';
$output_folder = 'block2';

// Удаляем все файлы в выходной папке
if (file_exists($output_folder)) {
    $files = glob($output_folder . '/*');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
} else {
    mkdir($output_folder, 0777, true);
}

// Функция для создания изображения куба
function createCubeImage($img) {
    $new_img = imagecreatetruecolor(32, 32);
    imagesavealpha($new_img, true);
    $trans_color = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
    imagefill($new_img, 0, 0, $trans_color);

    // Лицевая грань
    imagecopy($new_img, $img, 8, 8, 0, 0, 16, 16);

    // Верхняя грань (параллелограмм, отражение по горизонтали)
    for ($i = 0; $i < 8; $i++) {
        imagecopyresampled($new_img, $img, 8 + $i, $i, 0, 0, 16, 1, 16, 1);
    }

    // Боковая грань (параллелограмм, отражение по вертикали)
    for ($i = 0; $i < 8; $i++) {
        imagecopyresampled($new_img, $img, 24 - $i, 8 + $i, 0, 0, 1, 16, 1, 16);
    }

    return $new_img;
}

// Проходим по всем файлам в папке с исходными изображениями
foreach (scandir($input_folder) as $filename) {
    if (pathinfo($filename, PATHINFO_EXTENSION) === 'png') {
        $img_path = $input_folder . '/' . $filename;
        $img = imagecreatefrompng($img_path);

        // Проверяем размер изображения
        if (imagesx($img) == 32 && imagesy($img) == 32) {
            // Создаем изображение куба
            $new_img = createCubeImage($img);

            // Сохраняем преобразованное изображение в выходную папку
            $output_path = $output_folder . '/' . $filename;
            imagepng($new_img, $output_path);

            // Освобождаем память
            imagedestroy($new_img);
        }

        // Освобождаем память
        imagedestroy($img);
    }
}

echo 'Конвертация завершена.';
?>