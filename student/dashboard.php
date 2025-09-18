<?php
require_once '../config/config.php';
require_once '../lib/functions.php';
require_role('siswa');

// Fetch student's report history
$student_user_id = $_SESSION['user_id'];
$history_stmt = $mysqli->prepare("SELECT id, report_time, status FROM reports WHERE student_user_id = ? ORDER BY report_time DESC");
$history_stmt->bind_param("i", $student_user_id);
$history_stmt->execute();
$result = $history_stmt->get_result();
$reports = $result->fetch_all(MYSQLI_ASSOC);
$history_stmt->close();

function get_status_badge($status) {
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Student Dashboard</a>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container pt-5">
        <div class="text-center my-4">
            <h1>Selamat Datang, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
            <p>Jika Anda dalam keadaan darurat, tekan tombol di bawah ini.</p>
        </div>

        <div class="panic-button-container">
            <button id="panicButton" class="btn btn-danger btn-lg panic-button">TEKAN TOMBOL PANIK</button>
        </div>

        <div id="statusMessage" class="alert" style="display: none;"></div>

        <div class="mt-5">
            <h3>Riwayat Laporan Anda</h3>
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Laporan</th>
                            <th>Waktu</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="reportsTableBody">
                        <?php if (count($reports) > 0): ?>
                            <?php foreach ($reports as $report): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($report['id']); ?></td>
                                    <td><?php echo htmlspecialchars($report['report_time']); ?></td>
                                    <td><?php echo get_status_badge($report['status']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Anda belum membuat laporan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="../assets/js/student.js"></script>
</body>
</html>
