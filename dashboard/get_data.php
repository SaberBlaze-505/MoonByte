<?php
require 'db.php';

// Ambil data terbaru
$latest = $conn->query("SELECT * FROM pzem_data ORDER BY timestamp DESC LIMIT 1")->fetch_assoc();

// Ambil data 24 jam terakhir untuk grafik
$historyQuery = $conn->query("
    SELECT DATE_FORMAT(timestamp, '%H:%i') as time, power
    FROM pzem_data
    WHERE timestamp >= NOW() - INTERVAL 1 DAY
    ORDER BY timestamp ASC
");

$history = [];
while ($row = $historyQuery->fetch_assoc()) {
    $history[] = $row;
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'voltage' => (float) ($latest['voltage'] ?? 0),
    'current' => (float) ($latest['current'] ?? 0),
    'power'   => (float) ($latest['power'] ?? 0),
    'energy'  => (float) ($latest['energy'] ?? 0),
    'history' => $history
]);
?>