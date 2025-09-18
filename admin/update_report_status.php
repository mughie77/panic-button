<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

// Ensure the user is a logged-in admin
require_role('guru');

header('Content-Type: application/json');

// Check if it's a POST request and the necessary data is present
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['report_id']) && isset($_POST['status'])) {
    $report_id = intval($_POST['report_id']);
    $status = $_POST['status'];

    // Validate status value to prevent arbitrary data injection
    $allowed_statuses = ['Belum Diproses', 'Dalam Penanganan', 'Selesai', 'Laporan Palsu'];
    if (!in_array($status, $allowed_statuses)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid.']);
        exit;
    }

    // Prepare and execute the update statement
    $stmt = $mysqli->prepare("UPDATE reports SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $report_id);

    if ($stmt->execute()) {
        // Success
        echo json_encode(['success' => true, 'message' => 'Status laporan berhasil diperbarui.']);
    } else {
        // Failure
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status laporan.']);
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Invalid request
    echo json_encode(['success' => false, 'message' => 'Permintaan tidak valid.']);
}
?>
