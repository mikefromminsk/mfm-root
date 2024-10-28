<?php

error_reporting(1);

header('Content-Type: application/json');

$root = $_SERVER[DOCUMENT_ROOT];

$tasks = [];
$story_points = [];
foreach (array_filter(glob("$root/mfm*"), 'is_dir') as $dir) {
    $dir_name = basename($dir);
    if (file_exists("$dir/todos.txt")) {
        $file = file_get_contents("$dir/todos.txt");
        $lines = explode(PHP_EOL, $file);
        $not_null_lines = array_filter($lines, function ($line) {
            return strlen(trim($line)) > 1;
        });

        foreach ($not_null_lines as $line) {
            $count = substr_count($line, '$');
            if ($count > 0)
                $story_points[str_repeat("$", $count)] += 1;
        }

        $tasks[$dir_name] = count($not_null_lines);
    }
}

for ($i = 0; $i < 6; $i++) {
    $response[story_points_info][str_repeat("$", $i + 1)] = pow(2, $i) . " days";
}

$response[tasks] = $tasks;
foreach ($tasks as $key => $value) {
    $response[tasks_total] += $value;
}

$response[story_points] = $story_points;
foreach ($story_points as $key => $value) {
    $response[total_days] += pow(2, strlen($key) - 1) * $value;
}

$response["senior 200$/day"] = $response[total_days] * 200;
$response["middle 100$/day"] = $response[total_days] * 100;
$response["junior 50$/day"] = $response[total_days] * 50;

echo json_encode($response, JSON_PRETTY_PRINT);