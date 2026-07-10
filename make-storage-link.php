<?php
header('Content-Type: text/plain');

$root   = __DIR__;
$link   = $root . '/public/storage';
$target = $root . '/storage/app/public';

echo "LINK  : $link\n";
echo "TARGET: $target\n\n";

echo "target exists? " . (is_dir($target) ? "YES" : "NO") . "\n";
echo "link exists?   " . (file_exists($link) ? "YES" : "NO") . "\n";
echo "is symlink?    " . (is_link($link) ? "YES" : "NO") . "\n\n";

if (!is_dir($target)) {
    echo "ERROR: target folder tidak ada. Harusnya: storage/app/public\n";
    exit;
}

if (file_exists($link) && !is_link($link)) {
    echo "ERROR: public/storage sudah ada tapi itu folder/file biasa. Rename dulu.\n";
    exit;
}

if (is_link($link)) {
    unlink($link);
    echo "OK: hapus symlink lama\n";
}

if (@symlink($target, $link)) {
    echo "OK: symlink dibuat\n";
} else {
    echo "ERROR: gagal bikin symlink. Hosting mungkin blok symlink.\n";
}
