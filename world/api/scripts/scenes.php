<?php
include_once $_SERVER["DOCUMENT_ROOT"] . "/data/utils.php";

$val = get_required(width);
$val = get_required(height);
$val = get_required(type); // луга, лес
$val = get_required(background);
$val = get_required(objects);


function loadWorldData($worldFilePath) {
    return json_decode(file_get_contents($worldFilePath), true);
}

function saveWorldData($worldFilePath, $worldData) {
    file_put_contents($worldFilePath, json_encode($worldData, JSON_PRETTY_PRINT));
}

function createMatrix($width, $height) {
    $matrix = [];
    for ($i = 0; $i < $height; $i++) {
        $matrix[$i] = array_fill(0, $width, null);
    }
    return $matrix;
}

function placeObjectsInMatrix(&$matrix, $objects) {
    foreach ($objects as $object) {
        for ($y = $object['y']; $y < $object['y'] + $object['height']; $y++) {
            for ($x = $object['x']; $x < $object['x'] + $object['width']; $x++) {
                if (isset($matrix[$y][$x])) {
                    return false; // Объекты пересекаются
                }
                $matrix[$y][$x] = $object['id'];
            }
        }
    }
    return true;
}

function addObjectsToScene($worldFilePath, $sceneId, $owner, $newObjects) {
    $worldData = loadWorldData($worldFilePath);

    if (!isset($worldData['scene'][$sceneId])) {
        die("Error: Base does not exist.");
    }

    if ($worldData['scene'][$sceneId]['owner'] !== $owner) {
        die("Error: You do not have permission to modify this scene.");
    }

    $scene = $worldData['scene'][$sceneId];
    $width = $scene['width'];
    $height = $scene['height'];
    $existingObjects = $scene['objects'];

    $matrix = createMatrix($width, $height);

    if (!placeObjectsInMatrix($matrix, $existingObjects)) {
        die("Error: Existing objects overlap.");
    }

    if (!placeObjectsInMatrix($matrix, $newObjects)) {
        die("Error: New objects overlap with existing objects.");
    }

    foreach ($newObjects as $object) {
        $objectId = $object['id'];
        $worldData['scene'][$sceneId]['objects'][$objectId] = $object;
    }

    saveWorldData($worldFilePath, $worldData);
}

// Пример использования
$worldFilePath = 'path/to/scene.json';
$sceneId = 'scene1';
$owner = 'user1';
$newObjects = [
    [
        'id' => 'object3',
        'x' => 5,
        'y' => 5,
        'width' => 3,
        'height' => 3
    ],
    [
        'id' => 'object4',
        'x' => 10,
        'y' => 10,
        'width' => 3,
        'height' => 3
    ]
];

addObjectsToScene($worldFilePath, $sceneId, $owner, $newObjects);
?>