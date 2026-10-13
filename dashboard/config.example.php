<?php
// Copy this file to config.php and customize the values for your local environment.
// config.php is ignored by Git so local connection settings stay out of version control.
return [
    'database' => [
        'host' => '127.0.0.1',
        'username' => 'energy_monitor_user',
        'password' => 'replace-with-your-local-password',
        'name' => 'energy_monitor',
    ],
    'mqtt' => [
        'host' => '192.168.1.100',
        'port' => 1883,
        'topic' => 'pzem/data',
    ],
];
