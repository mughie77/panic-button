<?php
require_once 'includes/header.php';

// --- Fetch data for the main reports table ---
$reports_query = "
    SELECT
        r.id, r.report_time, r.status, u.username, s.full_name, s.class
    FROM reports r
    JOIN users u ON r.student_user_id = u.id
    JOIN students s ON u.id = s.user_id
    ORDER BY r.report_time DESC
";
$reports_result = $mysqli->query($reports_query);
$reports = $reports_result->fetch_all(MYSQLI_ASSOC);

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


function get_status_badge_admin($status) {
    switch ($status) {
        case 'Dalam Penanganan': return '<span class="badge bg-primary">Dalam Penanganan</span>';
        case 'Selesai': return '<span class="badge bg-success">Selesai</span>';
        case 'Laporan Palsu': return '<span class="badge bg-secondary">Laporan Palsu</span>';
        case 'Belum Diproses': default: return '<span class="badge bg-warning text-dark">Belum Diproses</span>';
    }
}

$mysqli->close();
?>

<div id="statusUpdateMessage" class="alert" style="display: none;"></div>
<audio id="notificationSound" src="../assets/notification.mp3" preload="auto"></audio>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Total']; ?></h5>
                <p class="card-text">Total Laporan</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-dark bg-warning">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Belum Diproses']; ?></h5>
                <p class="card-text">Belum Diproses</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Dalam Penanganan']; ?></h5>
                <p class="card-text">Ditangani</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Selesai']; ?></h5>
                <p class="card-text">Selesai</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-secondary">
            <div class="card-body">
                <h5 class="card-title"><?php echo $summary['Laporan Palsu']; ?></h5>
                <p class="card-text">Laporan Palsu</p>
            </div>
        </div>
    </div>
</div>


<!-- Chart and Reports Table -->
<div class="row">
    <!-- Chart -->
    <div class="col-lg-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Grafik Status Laporan</h5>
            </div>
            <div class="card-body">
                <canvas id="reportChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Reports Table -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Daftar Laporan Panik</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="reportsTable">
                        <!-- Table content from before -->
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Waktu</th>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($reports) > 0): $i = 1; foreach ($reports as $report): ?>
                                <tr id="report-row-<?php echo $report['id']; ?>">
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo $report['report_time']; ?></td>
                                    <td><?php echo htmlspecialchars($report['full_name']); ?></td>
                                    <td><?php echo htmlspecialchars($report['class']); ?></td>
                                    <td class="status-cell"><?php echo get_status_badge_admin($report['status']); ?></td>
                                    <td>
                                        <select class="form-select form-select-sm status-select" data-report-id="<?php echo $report['id']; ?>">
                                            <option value="Belum Diproses" <?php echo ($report['status'] == 'Belum Diproses') ? 'selected' : ''; ?>>Belum Diproses</option>
                                            <option value="Dalam Penanganan" <?php echo ($report['status'] == 'Dalam Penanganan') ? 'selected' : ''; ?>>Dalam Penanganan</option>
                                            <option value="Selesai" <?php echo ($report['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                                            <option value="Laporan Palsu" <?php echo ($report['status'] == 'Laporan Palsu') ? 'selected' : ''; ?>>Laporan Palsu</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="6" class="text-center">Tidak ada laporan panik yang ditemukan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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
