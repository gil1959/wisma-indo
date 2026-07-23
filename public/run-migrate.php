<?php
// A simple standalone script to run migrations.
// Simply visit: https://yourdomain.com/run-migrate.php

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Running migrations...<br><br>";

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    echo "<b>Migration successful!</b><br><br>Output: <br>";
    echo nl2br(\Illuminate\Support\Facades\Artisan::output());
} catch (\Exception $e) {
    echo "<b>Migration failed:</b> " . $e->getMessage();
}
