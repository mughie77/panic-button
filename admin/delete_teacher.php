<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Gagal menghapus data.'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if ($user_id > 0) {
        if ($user_id == $_SESSION['user_id']) {
            $response['message'] = 'Anda tidak dapat menghapus akun Anda sendiri.';
        } else {
            $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ? AND role = 'guru'");
            $stmt->bind_param("i", $user_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $response['success'] = true;
                    $response['message'] = 'Data guru berhasil dihapus.';
                } else {
                    $response['message'] = 'Data guru tidak ditemukan atau Anda tidak memiliki izin.';
                }
            }
            $stmt->close();
        }
    } else {
        $response['message'] = 'ID guru tidak valid.';
    }
}

$mysqli->close();
echo json_encode($response);
?>
