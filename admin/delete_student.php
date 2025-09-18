<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Gagal menghapus data.'];

// Use POST method for delete operations to prevent CSRF vulnerabilities
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id > 0) {
        // The ON DELETE CASCADE constraint handles the deletion from the 'students' table.
        $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'siswa'");
        $stmt->bind_param("i", $user_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Data siswa berhasil dihapus.';
            } else {
                $response['message'] = 'Data siswa tidak ditemukan atau Anda tidak memiliki izin.';
            }
        }
        $stmt->close();
    } else {
        $response['message'] = 'ID siswa tidak valid.';
    }
}

$mysqli->close();
echo json_encode($response);
?>
