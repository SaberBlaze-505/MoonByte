<?php
$configPath = __DIR__ . '/config.php';
if (!is_file($configPath)) {
    http_response_code(500);
    exit('Missing dashboard/config.php. Copy config.example.php to config.php and edit the local settings.');
}

$config = require $configPath;
$db = $config['database'] ?? [];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = new mysqli(
    $db['host'] ?? '127.0.0.1',
    $db['username'] ?? '',
    $db['password'] ?? '',
    $db['name'] ?? 'energy_monitor'
);
$conn->set_charset('utf8mb4');
