<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<pre>";

echo "PHP OK\n";
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Docroot: " . __DIR__ . "\n\n";

$autoload = __DIR__ . "/vendor/autoload.php";
echo "autoload path: $autoload\n";
echo "autoload exists: " . (file_exists($autoload) ? "YES" : "NO") . "\n\n";

$app = __DIR__ . "/bootstrap/app.php";
echo "app path: $app\n";
echo "app exists: " . (file_exists($app) ? "YES" : "NO") . "\n\n";

$env = __DIR__ . "/.env";
echo ".env exists: " . (file_exists($env) ? "YES" : "NO") . "\n";

echo "</pre>";
