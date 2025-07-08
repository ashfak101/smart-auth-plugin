<?php

// Define the plugin directory and the output ZIP file
$pluginDir = __DIR__;
$desktopDir = getenv('HOME') . '/Desktop';
$zipFile = $desktopDir . '/smart-auth.zip';

// Initialize the ZIP archive
$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    exit("Cannot open <$zipFile>\n");
}


// Create a recursive directory iterator
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($pluginDir),
    RecursiveIteratorIterator::LEAVES_ONLY
);

// Define the allowed paths
$allowedPaths = [
    'app/',
    'assets/',
    'autoloader.php',
    'smart-auth.php'
];

// Add files to the ZIP archive
foreach ($files as $name => $file) {
    // Skip directories (they are added automatically)
    if (!$file->isDir()) {
        // Get the real and relative path for the current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($pluginDir) + 1);

        // Check if the file is in the allowed paths
        foreach ($allowedPaths as $allowedPath) {
            if (strpos($relativePath, $allowedPath) === 0) {
                // Add the current file to the ZIP archive
                $zip->addFile($filePath, $relativePath);
                break;
            }
        }
    }
}

// Close the ZIP archive
$zip->close();

echo "Plugin packaged successfully as $zipFile\n";