<?php

$target = __DIR__ . '/storage/app/public';
$link   = __DIR__ . '/public/storage';

if (file_exists($link)) {
    echo "public/storage sudah ada\n";
    exit;
}

if (!is_dir($target)) {
    echo "TARGET TIDAK ADA\n";
    exit;
}

symlink($target, $link);
echo "SYMLINK BERHASIL\n";
