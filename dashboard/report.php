<?php
require 'db.php';

$sql = "
    SELECT 
        DATE(timestamp) as date,
        AVG(voltage) as voltage,
        AVG(current) as current,
        SUM(energy) as energy
    FROM pzem_data
    WHERE timestamp >= NOW() - INTERVAL 30 DAY
    GROUP BY DATE(timestamp)
    ORDER BY date DESC
";

$result = $conn->query($sql);
$report = [];
while ($row = $result->fetch_assoc()) {
    $report[] = $row;
}

echo json_encode($report);
?>