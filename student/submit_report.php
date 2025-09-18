<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

// Ensure the user is a logged-in student
require_role('siswa');

// Set content type to JSON for the response
header('Content-Type: application/json');

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_user_id = $_SESSION['user_id'];

    // Prepare and execute the insert statement
    $stmt = $mysqli->prepare("INSERT INTO reports (student_user_id) VALUES (?)");
    $stmt->bind_param("i", $student_user_id);

    if ($stmt->execute()) {
        // Success
        $new_report_id = $mysqli->insert_id;
        echo json_encode([
            'success' => true,
            'message' => 'Laporan berhasil dikirim.',
            'report' => [
                'id' => $new_report_id,
                'report_time' => date('Y-m-d H:i:s'),
                'status' => 'Belum Diproses'
            ]
        ]);
    } else {
        // Failure
        echo json_encode(['success' => false, 'message' => 'Gagal mengirim laporan.']);
    }

    $stmt->close();
    $mysqli->close();
} else {
    // Not a POST request
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
