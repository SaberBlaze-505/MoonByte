<?php
require 'db.php';
require __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;

// Query data 30 hari terakhir
$sql = "
    SELECT 
        DATE(timestamp) as date,
        AVG(voltage) as voltage,
        AVG(current) as current,
        SUM(energy) as energy
    FROM pzem_data
    WHERE timestamp >= NOW() - INTERVAL 30 DAY
    GROUP BY DATE(timestamp)
    ORDER BY date ASC
";

$result = $conn->query($sql);
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

if (empty($data)) {
    die("Tidak ada data untuk membuat laporan.");
}

// Buat HTML untuk PDF
$html = "
    <h2 style='text-align:center; color:#1f334b;'>Monthly Energy Usage Report</h2>
    <p style='text-align:center;'>Generated on " . date('Y-m-d H:i:s') . "</p>
    <br>
    <table border='1' cellspacing='0' cellpadding='6' width='100%' style='border-collapse:collapse; font-size:12px;'>
        <tr style='background:#1f334b; color:white;'>
            <th>Tanggal</th>
            <th>Rata-rata Tegangan (V)</th>
            <th>Rata-rata Arus (A)</th>
            <th>Total Energi (Wh)</th>
        </tr>
";

$totalVoltage = 0;
$totalCurrent = 0;
$totalEnergy = 0;

foreach ($data as $row) {
    $html .= "
        <tr>
            <td style='text-align:center;'>{$row['date']}</td>
            <td style='text-align:center;'>" . number_format($row['voltage'], 2) . "</td>
            <td style='text-align:center;'>" . number_format($row['current'], 3) . "</td>
            <td style='text-align:center;'>" . number_format($row['energy'], 2) . "</td>
        </tr>
    ";
    $totalVoltage += $row['voltage'];
    $totalCurrent += $row['current'];
    $totalEnergy  += $row['energy'];
}

$days = count($data);
$avgVoltage = $totalVoltage / $days;
$avgCurrent = $totalCurrent / $days;

$html .= "</table><br><br>
    <h4>Summary:</h4>
    <p>
        - Rata-rata Tegangan: <b>" . number_format($avgVoltage, 2) . " V</b><br>
        - Rata-rata Arus: <b>" . number_format($avgCurrent, 3) . " A</b><br>
        - Total Energi 30 hari terakhir: <b>" . number_format($totalEnergy, 2) . " Wh</b>
    </p>
";

// Buat PDF pakai Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Output ke browser (langsung download)
$filename = "Monthly_Report_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
?>