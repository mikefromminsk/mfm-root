<?php
$newVersion = '1.0.5';
$directories = glob('mfm*', GLOB_ONLYDIR);
foreach ($directories as $dir) {
    $packageJsonPath = $dir . '/package.json';
    if (file_exists($packageJsonPath)) {
        $packageJson = file_get_contents($packageJsonPath);
        $packageData = json_decode($packageJson, true);
        if (isset($packageData['version'])) {
            $packageData['version'] = $newVersion;
        }
        file_put_contents($packageJsonPath, json_encode($packageData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

echo "Press any key to continue...\n";