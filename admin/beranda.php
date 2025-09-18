<?php
require_once 'includes/header.php';

// --- Fetch data for analytics (summary and chart) ---
$analytics_query = "SELECT status, COUNT(*) as count FROM reports GROUP BY status";
$analytics_result = $mysqli->query($analytics_query);
$stats = $analytics_result->fetch_all(MYSQLI_ASSOC);

// Prepare data for summary cards and chart
$summary = [
    'Total' => 0,
    'Belum Diproses' => 0,
    'Dalam Penanganan' => 0,
    'Selesai' => 0,
    'Laporan Palsu' => 0
];
foreach ($stats as $stat) {
    $summary[$stat['status']] = $stat['count'];
    $summary['Total'] += $stat['count'];
}

// Prepare data for Chart.js
$chart_labels = array_keys($summary);
$chart_data = array_values($summary);
unset($chart_labels[0]); // Remove 'Total' from labels
unset($chart_data[0]);   // Remove 'Total' from data

$chart_labels_json = json_encode(array_values($chart_labels));
$chart_data_json = json_encode(array_values($chart_data));

$mysqli->close();
?>

<h1 class="h2 mb-4">Beranda</h1>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Total']; ?></h5>
                <p class="card-text">Total Laporan</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-dark bg-warning shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Belum Diproses']; ?></h5>
                <p class="card-text">Belum Diproses</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Dalam Penanganan']; ?></h5>
                <p class="card-text">Ditangani</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Selesai']; ?></h5>
                <p class="card-text">Selesai</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary shadow-sm">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Laporan Palsu']; ?></h5>
                <p class="card-text">Laporan Palsu</p>
            </div>
        </div>
    </div>
</div>

<!-- Chart -->
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grafik Status Laporan</h5>
            </div>
            <div class="card-body" style="height: 400px;">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>
</div>


<!-- Pass data to JS for the chart -->
<script>
    const reportChartData = {
        labels: <?php echo $chart_labels_json; ?>,
        data: <?php echo $chart_data_json; ?>
    };
</script>

<?php
require_once 'includes/footer.php';
?>
