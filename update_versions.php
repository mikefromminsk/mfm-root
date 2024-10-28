<?php
require_once 'versions.php';

foreach (getVersionChanges()[updates] as $change) {
    $packageJsonPath = "/wamp/www/node_modules/$change[folder]/package.json";
    $packageJson = json_decode(file_get_contents($packageJsonPath), true);
    $packageJson['version'] = $change['change'];
    file_put_contents($packageJsonPath, json_encode($packageJson, JSON_PRETTY_PRINT));
}
