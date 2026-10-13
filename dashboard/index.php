<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Monitor Dashboard</title>
    <link rel="stylesheet" href="assets/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
</head>

<body>
    <header>
        <h1>⚡ Energy Monitor Dashboard</h1>
        <p>Real-time data from PZEM sensors</p>
    </header>

    <section class="cards">
        <div class="card" id="voltageCard">Voltage<br><span id="voltage">--</span> V</div>
        <div class="card" id="currentCard">Current<br><span id="current">--</span> A</div>
        <div class="card" id="energyCard">Energy<br><span id="energy">--</span> kWh</div>
        <div class="card" id="powerCard">Power<br><span id="power">--</span> W</div>
    </section>

    <section class="chart-section">
        <h2>Power Usage Graph (Last 24 Hours)</h2>
        <canvas id="powerChart"></canvas>
    </section>

    <section class="report-section">
        <h2>Monthly Report</h2>
        <p>Laporan otomatis berdasarkan 30 hari terakhir.</p>
        <a href="generate_report.php" class="btn-download">Download Monthly Report (PDF)</a>
        <table id="reportTable">
            <thead>
                <tr><th>Date</th><th>Avg. Voltage (V)</th><th>Avg. Current (A)</th><th>Energy Total</th></tr>
            </thead>
            <tbody></tbody>
        </table>
    </section>


    <script>
    async function fetchData() {
        const res = await fetch('get_data.php');
        const data = await res.json();
        if (!data) return;

        document.getElementById('voltage').textContent = data.voltage.toFixed(2);
        document.getElementById('current').textContent = data.current.toFixed(2);
        document.getElementById('power').textContent = data.power.toFixed(2);
        document.getElementById('energy').textContent = data.energy.toFixed(3);

        updateChart(data.history);
    }

    async function loadReport() {
        const res = await fetch('report.php');
        const report = await res.json();
        const tbody = document.querySelector('#reportTable tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        report.forEach(row => {
            tbody.innerHTML += `
                    <tr>
                        <td>${row.date}</td>
                        <td>${row.voltage.toFixed(2)}</td>
                        <td>${row.current.toFixed(2)}</td>
                        <td>${row.energy.toFixed(3)}</td>
                    </tr>`;
        });
    }

    let chart;

    function updateChart(history) {
        const ctx = document.getElementById('powerChart').getContext('2d');
        if (chart) chart.destroy();
        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: history.map(h => h.time),
                datasets: [{
                    label: 'Power (W)',
                    data: history.map(h => h.power),
                    fill: false,
                    borderWidth: 2,
                    tension: 0.3
                }]
            }
        });
    }

    fetchData();
    loadReport();
    setInterval(fetchData, 5000);
    </script>
</body>

</html>