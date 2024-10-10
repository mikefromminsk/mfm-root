<?php

require_once 'versions.php';

foreach (getVersionChanges() as $change) {
    $packageJsonPath = "../$change[name]/package.json";
    $packageJson = json_decode(file_get_contents($packageJsonPath), true);
    $packageJson['version'] = $change['into'];
    file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT));
}
