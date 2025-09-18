<?php
require_once '../config/config.php';
require_once '../lib/functions.php';

require_role('guru');
header('Content-Type: application/json');

$response = ['success' => false, 'data' => null, 'message' => 'Gagal mengambil data siswa.'];
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($user_id > 0) {
    $stmt = $mysqli->prepare("
        SELECT s.full_name, s.class, u.username
        FROM students s
        JOIN users u ON s.user_id = u.id
        WHERE u.id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if ($student) {
        $response['success'] = true;
        $response['data'] = $student;
        $response['message'] = 'Data siswa berhasil diambil.';
    }
}

$mysqli->close();
echo json_encode($response);
?>
