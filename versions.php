<?php

error_reporting(1);

header("Content-type: application/json;charset=utf-8");

function executeCommand($command, $directory = null)
{
    $output = [];
    $returnVar = 0;
    if ($directory) {
        $currentDir = getcwd();
        chdir($directory);
    }
    exec($command, $output, $returnVar);
    if ($directory) {
        chdir($currentDir);
    }
    if ($returnVar !== 0) {
        throw new Exception("Command failed: $command\nOutput: " . implode("\n", $output));
    }
    return $output;
}

function getDirectoryContents($dir) {
    $contents = [];
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

    foreach ($iterator as $file) {
        if (strpos($file->getRealPath(), DIRECTORY_SEPARATOR . '.') !== false) {
            continue;
        }
        if ($file->isFile()) {
            $relativePath = str_replace($dir, '', $file->getPathname());
            $contents[$relativePath] = [
                'size' => $file->getSize(),
                'mtime' => $file->getMTime(),
                'hash' => hash_file('md5', $file->getPathname())
            ];
        }
    }

    return $contents;
}

function getFileHash($filePath)
{
    return hash_file('sha256', $filePath);
}

function direcroriesNotEquals($dir1, $dir2)
{
    $contents1 = getDirectoryContents($dir1);
    $contents2 = getDirectoryContents($dir2);

    $differences = array_diff_assoc($contents1, $contents2);

    return $differences != [];
}

function getHighestVersion()
{
    $highestVersion = '0.0.0';
    foreach (glob('/tmp/node_modules/mfm*', GLOB_ONLYDIR) as $dir) {
        $packageJsonPath = $dir . '/package.json';
        if (file_exists($packageJsonPath)) {
            $packageJson = file_get_contents($packageJsonPath);
            $packageData = json_decode($packageJson, true);
            if (isset($packageData['version'])) {
                $current_parts = explode('.', $packageData['version']);
                $highest_parts = explode('.', $highestVersion);
                for ($i = 0; $i < sizeof($current_parts); $i++) {
                    if (doubleval($current_parts[$i]) > doubleval($highest_parts[$i])) {
                        $highestVersion = $packageData['version'];
                        break;
                    } else if (doubleval($current_parts[$i]) < doubleval($highest_parts[$i])) {
                        break;
                    }
                }
            }
        }
    }
    return $highestVersion;
}

function incrementVersion($version)
{
    $parts = explode('.', $version);
    return implode('.', [$parts[0], $parts[1], $parts[2] + 1]);
}

function rrmdir($dir)
{
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir . "/" . $object) == "dir") rrmdir($dir . "/" . $object); else unlink($dir . "/" . $object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}


function getVersionChanges()
{
    $tmp = '/tmp';
    if (file_exists($tmp)) {
        rrmdir($tmp);
    }
    mkdir($tmp, 0777, true);
    executeCommand('npm install mfm-wallet', $tmp);
    $highestVersion = getHighestVersion();
    $newVersion = incrementVersion($highestVersion);
    $response = [];
    foreach (array_filter(glob("$tmp/node_modules/mfm*"), 'is_dir') as $nodeSubDir) {
        $subDir = substr($nodeSubDir, strpos($nodeSubDir, "node_modules/") + strlen("node_modules/"));
        $dir = "/wamp/www/node_modules/$subDir";
        if (direcroriesNotEquals($dir, $nodeSubDir)) {
            $response[] = [
                'name' => $subDir,
                'curr' => json_decode(file_get_contents("$nodeSubDir/package.json"), true)['version'],
                'from' => json_decode(file_get_contents("$dir/package.json"), true)['version'],
                'into' => $newVersion
            ];
        }
    }
    rrmdir('/tmp');
    return $response;
}

echo json_encode(getVersionChanges(), JSON_PRETTY_PRINT);