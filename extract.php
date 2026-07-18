<?php
echo "Starting deployment fix...\n";
$basePath = __DIR__;
$vendorDir = $basePath . '/vendor';
$zipFile = $basePath . '/vendor.zip';
$cacheDir = $basePath . '/bootstrap/cache';

// 1. Delete old vendor
if (is_dir($vendorDir)) {
    echo "Deleting old vendor directory...\n";
    exec('rm -rf ' . escapeshellarg($vendorDir));
}

// 2. Extract zip
if (file_exists($zipFile)) {
    echo "Extracting vendor.zip...\n";
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($basePath);
        $zip->close();
        echo "Extracted successfully.\n";
    } else {
        echo "Failed to open vendor.zip!\n";
    }
} else {
    echo "vendor.zip not found!\n";
}

// 3. Clear cache
if (is_dir($cacheDir)) {
    echo "Clearing bootstrap cache...\n";
    $files = glob($cacheDir . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "Cache cleared.\n";
}

echo "Deployment fix complete.\n";
