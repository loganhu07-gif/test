<?php
// Lightweight bootstrap to wire up namespaced class loading and shared helpers.
spl_autoload_register(function ($class) {
    $prefix = 'app\\';
    $baseDir = __DIR__ . '/../';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relative) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

require_once __DIR__ . '/functions.php';

// Ensure storage directories exist.
foreach ([
    __DIR__ . '/../../storage/uploads',
    __DIR__ . '/../../storage/data',
    __DIR__ . '/../../storage/backups',
] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
}
