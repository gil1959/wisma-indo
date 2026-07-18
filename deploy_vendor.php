<?php
ini_set('max_execution_time', 300); // 5 minutes just in case

$zipFile = __DIR__ . '/vendor.zip';
$extractTo = __DIR__;

if (!file_exists($zipFile)) {
    die("<h1>ERROR: vendor.zip not found!</h1><p>Please make sure you have deployed the latest commit containing vendor.zip.</p>");
}

echo "<h1>Restoring Laravel Vendor Dependencies</h1>";
echo "<pre>";

// 1. Delete old vendor directory
$vendorDir = __DIR__ . '/vendor';
if (is_dir($vendorDir)) {
    echo "Deleting corrupted vendor folder...\n";
    
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($vendorDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($vendorDir);
    echo "Deletion complete.\n\n";
}

// 2. Extract ZIP
echo "Extracting vendor.zip...\n";
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    $zip->extractTo($extractTo);
    $zip->close();
    echo "Extraction complete!\n\n";
    echo "</pre>";
    echo "<h2>SUCCESS: The website is now fully restored and ready to use!</h2>";
    echo "<p><a href='/'>Go back to website</a></p>";
    
    // Clean up zip file
    unlink($zipFile);
} else {
    echo "</pre>";
    echo "<h2>FAILED: Could not extract vendor.zip.</h2>";
}
