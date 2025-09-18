<?php
require_once 'includes/header.php';

// Fetch all reports with student names
// We need to join reports -> users -> students
$query = "
    SELECT
        r.id,
        r.report_time,
        r.status,
        u.username,
        s.full_name
    FROM reports r
    JOIN users u ON r.student_user_id = u.id
    JOIN students s ON u.id = s.user_id
    ORDER BY r.report_time DESC
";

$result = $mysqli->query($query);
$reports = $result->fetch_all(MYSQLI_ASSOC);

function get_status_badge_admin($status) {
    switch ($status) {
        case 'Dalam Penanganan':
            return '<span class="badge bg-primary">Dalam Penanganan</span>';
        case 'Selesai':
            return '<span class="badge bg-success">Selesai</span>';
        case 'Belum Diproses':
        default:
            return '<span class="badge bg-warning text-dark">Belum Diproses</span>';
    }
}

$mysqli->close();
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Laporan Panik</h1>
</div>

<div id="statusUpdateMessage" class="alert" style="display: none;"></div>

<!-- Audio element for notification sound -->
<audio id="notificationSound" src="../assets/notification.mp3" preload="auto"></audio>

<div class="table-responsive">
    <table class="table table-striped table-sm" id="reportsTable">
        <thead>
            <tr>
                <th>ID Laporan</th>
                <th>Waktu</th>
                <th>Nama Siswa</th>
                <th>Username</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($reports) > 0): ?>
                <?php foreach ($reports as $report): ?>
                    <tr id="report-row-<?php echo $report['id']; ?>">
                        <td><?php echo $report['id']; ?></td>
                        <td><?php echo $report['report_time']; ?></td>
                        <td><?php echo htmlspecialchars($report['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($report['username']); ?></td>
                        <td class="status-cell"><?php echo get_status_badge_admin($report['status']); ?></td>
                        <td>
                            <select class="form-select form-select-sm status-select" data-report-id="<?php echo $report['id']; ?>">
                                <option value="Belum Diproses" <?php echo ($report['status'] == 'Belum Diproses') ? 'selected' : ''; ?>>Belum Diproses</option>
                                <option value="Dalam Penanganan" <?php echo ($report['status'] == 'Dalam Penanganan') ? 'selected' : ''; ?>>Dalam Penanganan</option>
                                <option value="Selesai" <?php echo ($report['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Tidak ada laporan panik yang ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
require_once 'includes/footer.php';
?>
